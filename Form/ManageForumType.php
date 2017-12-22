<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * Class ManageForumType
 *
 * @package Yosimitso\WorkingForumBundle\Form
 */
class ManageForumType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $list_forum
     */
    public function buildForm(FormBuilderInterface $builder, array $list_forum)
    {
        foreach ($list_forum as $forum) {
            $builder
                ->add(
                    'forum',
                    CollectionType::class,
                    [
                        'entry_type'   => \Yosimitso\WorkingForumBundle\Form\AdminForumType::class,
                        'allow_add'    => true,
                        'allow_delete' => true,
                    ]
                );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
            ]
        );
    }
}


