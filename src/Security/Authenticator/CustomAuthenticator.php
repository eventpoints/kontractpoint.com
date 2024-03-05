<?php

declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Entity\User;
use App\Repository\EmailRepository;
use App\Repository\PhoneNumberRepository;
use App\Repository\UserRepository;
use App\Service\EmailService\EmailHelperService;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class CustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepository $userRepository,
        private readonly EmailRepository $emailRepository,
        private readonly PhoneNumberRepository $phoneNumberRepository,
        private readonly EmailHelperService $emailHelperService
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $emailAddressOrPhoneNumber = preg_replace('/\s+/', '', $request->request->get('email', ''));
        if ($this->emailHelperService->isEmail($emailAddressOrPhoneNumber)) {
            $email = $this->emailRepository->findOneBy([
                'address' => $emailAddressOrPhoneNumber,
            ]);
        } else {
            $phoneNumber = $this->phoneNumberRepository->findByFullNumber($emailAddressOrPhoneNumber);
            $email = $phoneNumber->getOwner()?->getEmail();
        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email->getAddress());

        return new Passport(
            new UserBadge($email->getAddress()),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);

        $user = $token->getUser();
        if ($user instanceof User) {
            $user->setUpdatedAt(new CarbonImmutable());
            $this->userRepository->save($user, true);
        }

        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }
}
