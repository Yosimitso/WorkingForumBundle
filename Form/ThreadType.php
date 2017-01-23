<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class ThreadType
 *
 * @package Yosimitso\WorkingForumBundle\Form
 */
class ThreadType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'label',
                TextType::class,
                [
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                    'label'              => 'forum.thread',
                    'error_bubbling'     => true,
                ]
            )
            ->add(
                'sublabel',
                TextType::class,
                [
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                    'label'              => 'forum.sublabel',
                    'error_bubbling'     => true,
                ]
            )
            ->add(
                'post',
                CollectionType::class,
                [
                    'entry_type'     => PostType::class,
                    'allow_add'      => false,
                    'error_bubbling' => true,
                ]
            )
            ->add(
                'pin',
                CheckboxType::class,
                [
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                    'label'              => 'forum.doPin',
                    'required'           => false,
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
                'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Thread',

            ]
        );
    }

    /**
     * @return string
     */
    /*
    public function getName()
    {
        return 'yosimitso_working_forumbundle_thread';
    }*/
}