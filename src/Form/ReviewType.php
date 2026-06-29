<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Cég neve',
                'attr' => [
                    'placeholder' => 'Pl. Trustindex',
                    'class' => 'form-control',
                ],
            ])
            ->add('rating', IntegerType::class, [
                'label' => 'Értékelés',
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                    'placeholder' => '1 és 5 között',
                    'class' => 'form-control',
                ],
            ])
            ->add('reviewText', TextareaType::class, [
                'label' => 'Vélemény',
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Írd le a tapasztalatodat...',
                    'class' => 'form-control',
                ],
            ])
            ->add('authorEmail', EmailType::class, [
                'label' => 'Email címed',
                'attr' => [
                    'placeholder' => 'nev@example.com',
                    'class' => 'form-control',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Vélemény beküldése',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}