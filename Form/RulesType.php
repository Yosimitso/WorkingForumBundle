<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Yosimitso\WorkingForumBundle\Entity\Rules;

/**
 * Class RulesType
 *
 * @package Yosimitso\WorkingForumBundle\Form
 */
class RulesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'langs',
                EntityType::class,
                [
                    'class' => Rules::class,
                    'choice_label' => 'lang',
                    'choice_value' => 'lang',
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                    'label' => 'admin.rules_lang',
                    'error_bubbling' => true,
                    'attr' => ['id' => 'wf_edit_lang'],
                ]
            )
            ->add(
                'newLang',
                TextType::class,
                [
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
