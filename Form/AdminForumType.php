<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class AdminForumType
 *
 * @package Yosimitso\WorkingForumBundle\Form
 */
class AdminForumType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',
                TextType::class,
                [
                    'label'              => 'admin.forum_name',
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                ]
            )
            ->add('subforum',
                CollectionType::class,
                [
                    'entry_type'   => \Yosimitso\WorkingForumBundle\Form\AdminSubforumType::class,
                    'allow_add'    => true,
                    'allow_delete' => true,
                ]
            )
            ->add('submit',
                SubmitType::class,
                [
                    'label'              => 'admin.submit',
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                    'attr'               => ['class' => 'wf_button'],
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Forum',
            ]
        );
    }
}
