<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Yosimitso\WorkingForumBundle\Form\AdminSubforumType;

class AdminForumType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text',[ 'label' => 'admin.forum_name', 'translation_domain' => 'YosimitsoWorkingForumBundle'])
            ->add('subforum','collection',['type' => new AdminSubforumType(), 'allow_add' => true, 'allow_delete' => true])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Forum'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yosimitso_workingforumbundle_forum';
    }
}
