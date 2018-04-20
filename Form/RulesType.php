<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class RulesType
 *
 * @package Yosimitso\WorkingForumBundle\Form
 */
class RulesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'lang',
                ChoiceType::class,
                [
                    'choices' => $options['langs'],
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                    'label'              => 'admin.rules_lang',
                    'error_bubbling'     => true,
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'attr' => ['class' => 'wf_textarea_post']
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'langs'
        ]);
        
        $resolver->setDefaults(
            [
                'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Rules',

            ]
        );
    }
}