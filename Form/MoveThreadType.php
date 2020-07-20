<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class MoveThreadType
 *
 * @package Yosimitso\WorkingForumBundle\Form
 */
class MoveThreadType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'forum',
                EntityType::class,
                [
                    'class' => 'YosimitsoWorkingForumBundle:Subforum',
                    'choice_label' => 'name',
                    'multiple' => false,
                    'label' => 'search.search_in',
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                    'group_by' => function ($sub) {
                        return $sub->getForum()->getName();
                    },
                   'attr' => ['style' => 'display:none']

                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Post',
            ]
        );
    }
}
