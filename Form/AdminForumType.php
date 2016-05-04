<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yosimitso\WorkingForumBundle\Form\AdminSubforumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdminForumType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[ 'label' => 'admin.forum_name', 'translation_domain' => 'YosimitsoWorkingForumBundle'])
            ->add('subforum',CollectionType::class,['entry_type' => new AdminSubforumType(), 'allow_add' => true, 'allow_delete' => true])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Forum'
        ));
    }

    /**
     * @return string
     */
    /*
    public function getName()
    {
        return 'yosimitso_workingforumbundle_forum';
    }*/
}
