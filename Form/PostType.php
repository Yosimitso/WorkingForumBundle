<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class PostType
 *
 * @package Yosimitso\WorkingForumBundle\Form
 */
class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content',
                TextareaType::class,
                [
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                    'label' => 'forum.content',
                    'attr' => ['class' => 'wf_textarea_post'],
                ]
            )
            ->add('filesUploaded',
                CollectionType::class,
                [
                    'entry_type' => FileType::class,
                    'entry_options' => ['attr' => ['class' => 'wf_input_submit']],
                    'allow_add' => true,
                    'required' => false,
                    'label' => false
                ]
            );
        
        if ($options['canSubscribeThread']) {
            $builder->add('addSubscription',
                CheckboxType::class,
                [
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                    'label' => 'forum.subscribe',
                    'required' => false,
                ]
            );
        }


;    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'canSubscribeThread'
        ]);

        $resolver->setDefaults([
            'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Post',
        ]);
    }
}