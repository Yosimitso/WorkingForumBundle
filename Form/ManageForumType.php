<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yosimitso\WorkingForumBundle\Form\AdminForumType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
            ->add('forum',CollectionType::class,['type' => new AdminForumType(), 'allow_add' => true, 'allow_delete' => true])
        ;
        }
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    /**
     * @return string
     */
    /*
    public function getName()
    {
        return 'yosimitso_workingforumbundle_forum';
    }
    */
  
}


