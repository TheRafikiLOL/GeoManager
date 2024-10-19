<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Currency;
use App\Entity\Language;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('fullname')
            ->add('region')
            ->add('subregion')
            ->add('capital')
            ->add('area')
            ->add('population')
            ->add('flag')
            ->add('alpha2code')
            ->add('alpha3code')
            ->add('numericcode')
            /*->add('languages', EntityType::class, [
                'class' => Language::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'placeholder' => 'Seleccione idiomas',
                'required' => false,
                'attr' => [
                    'class'            => 'form-control select2',
                    'data-allow-clear' => 'true',
                    'data-tags'        => 'true'
                ]
            ])*/
            /*->add('currencies', EntityType::class, [
                'class' => Currency::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'placeholder' => 'Seleccione monedas',
                'required' => false,
                'attr' => [
                    'class'            => 'form-control select2',
                    'data-allow-clear' => 'true',
                    'data-tags'        => 'true'
                ]
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}
