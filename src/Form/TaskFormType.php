<?php

namespace App\Form;

use App\Entity\TodoTask;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TaskFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, ['required' => false, 'disabled' => true])
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a title',
                    ]),
                ],
            ])
            
            ->add('content', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('date', DateTimeType::class, [
                'mapped' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ]);
            if ($options['finished_checkbox']) {
             $builder   
             ->add('finished', CheckboxType::class, [
                 'mapped' => true,
                 'required' => false,
             ]);
            }
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TodoTask::class,
            'finished_checkbox' => false,
        ]);
    }
}
