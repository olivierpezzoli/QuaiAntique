<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Regex('/^[\p{L}\s\'-]+$/u', 'Le prénom ne peut contenir que des lettres, espaces, tirets et apostrophes'),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Vous ne pouvez pas utiliser plus de 50 caractères'
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Regex('/^[\p{L}\s\'-]+$/u', 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes'),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Vous ne pouvez pas utiliser plus de 50 caractères'
                    ]),
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Regex('/^[0-9]+$/', 'Vous ne pouvez utiliser que des chiffres'),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'Vous ne pouvez pas utiliser plus de 20 caractères'
                    ]),
                ],
            ])
            ->add('defaultAllergy', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Regex('/^[\p{L}\s,\'-]+$/u', 'Les allergies ne peuvent contenir que des lettres, espaces, virgules, tirets et apostrophes'),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Vous ne pouvez pas utiliser plus de 100 caractères'
                    ]),
                ],
            ])
            ->add('defaultNbPlaces', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new Range(['min' => 1, 'max' => 20, 'notInRangeMessage' => 'Le nombre de places doit être entre {{ min }} et {{ max }}']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}