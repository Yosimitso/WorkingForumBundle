<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Yosimitso\WorkingForumBundle\Entity\Form\Post;

class ThreadType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label','text',['translation_domain' => 'YosimitsoWorkingForumBundle','label' => 'forum.thread', 'error_bubbling' => true])
            ->add('sublabel','text',['translation_domain' => 'YosimitsoWorkingForumBundle','label' => 'forum.sublabel','error_bubbling' => true])
            ->add('post','collection',['type' => new PostType(), 'allow_add' => false, 'error_bubbling' => true])
            ->add('pin','checkbox',['translation_domain' => 'YosimitsoWorkingForumBundle', 'label' => 'forum.doPin'])
            
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Thread'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yosimitso_working_forumbundle_thread';
    }
}