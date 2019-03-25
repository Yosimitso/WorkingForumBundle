<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yosimitso\WorkingForumBundle\Entity\Censorship;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AdminCensorshipType
 *
 * @package Yosimitso\WorkingForumBundle\Form
 */
class AdminCensorshipType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('censored_words',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => null,
                    'translation_domain' => 'YosimitsoWorkingForumBundle',
                    'attr' => ['expanded' => true, 'id' => 'censorship_list']
                ]
            )
            ->add('censoredList',
                HiddenType::class,
                [
                    'data' => implode(',', $options['censoredList'])

                ])
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
        $resolver->setRequired([
            'censoredList'
        ]);

        $resolver->setDefaults(
            [
                'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Censorship'
            ]
        );
    }
}