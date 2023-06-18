<?php

namespace App\Form;

use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_MEMBER' => 'ROLE_MEMBER',
                    'ROLE_INSTRUCTOR' => 'ROLE_INSTRUCTOR',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('password')
            ->add('first_name')
            ->add('preprovision')
            ->add('last_name')
            ->add('date_of_birth')
            ->add('hiring_date')
            ->add('salary')
            ->add('social_sec_number')
            ->add('street')
            ->add('place')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
