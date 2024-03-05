<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'row_attr' => [
                ],
            ])
            ->add('lastName', TextType::class, [
                'row_attr' => [
                ],
            ])
            ->add('originCountry', CountryType::class, [
                'required' => true,
            ])
            ->add('currentCountry', CountryType::class, [
                'required' => true,
            ])
            ->add('avatar', FileType::class, [
                'row_attr' => [
                    'class' => 'w-75',
                ],
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
