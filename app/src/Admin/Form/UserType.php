<?php

namespace App\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'attr' => [
                    'minlength' => 6,
                    'maxlength' => 12,
                ],
            ])
            ->add('authorize', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
