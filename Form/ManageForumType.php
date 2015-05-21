<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Yosimitso\WorkingForumBundle\Form\AdminForumType;

class ManageForumType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $list_forum)
    {
        foreach ($list_forum as $forum)
        {
        $builder
            ->add('forum','collection',['type' => new AdminForumType(), 'allow_add' => true, 'allow_delete' => true])
        ;
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yosimitso_workingforumbundle_forum';
    }
    
    public function addForum($forum)
    {
        
    }
}


