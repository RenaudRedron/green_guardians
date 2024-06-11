<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Reporting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserReportingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Autre' => 0,
                    'Discours haineux' => 1,
                    'Spam' => 2,
                    'Fausses informations' => 3,
                    'Harcèlement' => 4,
                    'Contenu illégal' => 5,
                ],
                'multiple' => false,   
                'expanded' => false,
            ])
            ->add('content', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reporting::class,
        ]);
    }
}
