<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null, ['label'  => 'Adresse E-mail'])
            //->add('roles')
            ->add('password', null, ['label'  => 'Mot de passe', 'empty_data' => ''])
            ->add('name', null, ['label'  => 'Pseudo'])
            ->add('avatar', null, ['label'  => 'Avatar'])
            ->add('save', SubmitType::class, ['label'  => 'Inscription', 'attr' => ['class' => 'btn btn-success']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
