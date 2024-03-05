<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid|null $id = null;

    #[ORM\OneToOne]
    private ?Email $email = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Email::class, cascade: ['persist'])]
    private Collection $emails;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: PhoneNumber::class, cascade: ['persist'])]
    private Collection $phoneNumbers;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private null|PhoneNumber $phoneNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\OneToMany(targetEntity: Business::class, mappedBy: 'owner')]
    private Collection $businesses;

    #[ORM\Column(nullable: true)]
    private null|DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::TEXT,nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $originCountry = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $currentCountry = null;

    public function __construct()
    {
        $this->phoneNumbers = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->businesses = new ArrayCollection();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email->getAddress();
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, PhoneNumber>
     */
    public function getPhoneNumbers(): Collection
    {
        return $this->phoneNumbers;
    }

    public function addPhoneNumber(PhoneNumber $phoneNumber): static
    {
        if (! $this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers->add($phoneNumber);
            $phoneNumber->setOwner($this);
        }

        return $this;
    }

    public function removePhoneNumber(PhoneNumber $phoneNumber): static
    {
        // set the owning side to null (unless already changed)
        if ($this->phoneNumbers->removeElement($phoneNumber) && $phoneNumber->getOwner() === $this) {
            $phoneNumber->setOwner(null);
        }

        return $this;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumber $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Collection<int, Email>
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function addEmail(Email $email): static
    {
        if (! $this->emails->contains($email)) {
            $this->emails->add($email);
            $email->setOwner($this);
        }

        return $this;
    }

    public function removeEmail(Email $email): static
    {
        // set the owning side to null (unless already changed)
        if ($this->emails->removeElement($email) && $email->getOwner() === $this) {
            $email->setOwner(null);
        }

        return $this;
    }

    public function getEmail(): null|Email
    {
        return $this->email;
    }

    public function setEmail(null|Email $defaultEmail): static
    {
        $this->email = $defaultEmail;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Business>
     */
    public function getBusinesses(): Collection
    {
        return $this->businesses;
    }

    public function addBusiness(Business $business): static
    {
        if (!$this->businesses->contains($business)) {
            $this->businesses->add($business);
            $business->setOwner($this);
        }

        return $this;
    }

    public function removeBusiness(Business $business): static
    {
        if ($this->businesses->removeElement($business)) {
            // set the owning side to null (unless already changed)
            if ($business->getOwner() === $this) {
                $business->setOwner(null);
            }
        }

        return $this;
    }

    public function getUpdatedAt(): null|DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(null|DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getOriginCountry(): ?string
    {
        return $this->originCountry;
    }

    public function setOriginCountry(?string $originCountry): static
    {
        $this->originCountry = $originCountry;

        return $this;
    }

    public function getCurrentCountry(): ?string
    {
        return $this->currentCountry;
    }

    public function setCurrentCountry(?string $currentCountry): static
    {
        $this->currentCountry = $currentCountry;

        return $this;
    }
}
