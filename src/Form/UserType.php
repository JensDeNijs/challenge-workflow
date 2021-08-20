<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'ROLE_PLAYER' =>'ROLE_PLAYER',
                    'ROLE_AGENT' =>'ROLE_AGENT',
                    'ROLE_AGENT2' =>'ROLE_AGENT2',
                    'ROLE_ADMIN' =>'ROLE_ADMIN'
                ),
                'multiple' => true,
                'label' => 'Roles'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
