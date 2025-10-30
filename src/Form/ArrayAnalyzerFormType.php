<?php

namespace App\Form;

use App\Validator\Constraint\ArrayNumbers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArrayAnalyzerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('strategy', ChoiceType::class, [
                'label' => 'Wybierz rodzaj analizy',
                'choices' => [
                    'Kompletność' => 'completeness',
                    'Unikalność' => 'uniqueness',
                ],
            ])
            ->add('manualInput', CheckboxType::class, [
                'label' => 'Wprowadzę własne dane',
                'required' => false,
            ])
            ->add('numbers', TextareaType::class, [
                'label' => 'Podaj liczby (oddzielone przecinkami)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => '1,2,3,...',
                ],
                'constraints' => [new ArrayNumbers()],
            ])
            ->add('arraySize', IntegerType::class, [
                'label' => 'Liczba elementów tablicy do losowego wygenerowania',
                'required' => false,
                'data' => 10,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
