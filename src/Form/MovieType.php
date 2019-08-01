<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Repository\PeopleRepository;
use App\Repository\CategoryRepository;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $years = range(1960, date("Y"));

        $builder
            ->add('title', null, ['label' => "Titre"])
            ->add('slug', null, ['label' => "Slug"])
            ->add('poster', null, ['label' => "Affiche"])
            ->add('releasedAt', DateType::class, [
                'label' => "Sortie en salle",
                'placeholder' => ['year' => '--', 'month' => '--', 'day' => '--'],
                'format' => 'dd/MM/yyyy',
                'years' => $years
            ])
            ->add('synopsys', null, ['label' => "Synopsys"])
            ->add('categories', null, [
                'label' => "Catégories",
                'expanded' => true,
                'query_builder' => function (CategoryRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.title', 'ASC');
                },
            ])
            ->add('actors', null, [
                'label' => "Acteurs",
                'expanded' => true,
                'query_builder' => function (PeopleRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.lastName', 'ASC');
                },
            ])
            ->add('director', null, [
                'label' => "Réalisateur",
                'placeholder' => '',
                'empty_data'  => null,
                'query_builder' => function (PeopleRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->innerJoin('p.directed', 'm')
                        ->orderBy('p.lastName', 'ASC');
                },
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $movie = $event->getData();
            $form = $event->getForm();

            if (!$movie || null === $movie->getId()) {
                $form->remove('slug');
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
