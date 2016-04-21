<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('keywords','text',['translation_domain' => 'YosimitsoWorkingForumBundle','label' => 'search.keywords'])
            ->add('forum','entity',[
                'class' => 'YosimitsoWorkingForumBundle:Subforum',
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'search.search_in',
                'translation_domain' => 'YosimitsoWorkingForumBundle',
                'group_by' => function($sub)
            {
                return $sub->getForum()->getName();
            }
                
            ])
            ->add('submit','submit',['label' => 'forum.search_forum', 'translation_domain' => 'YosimitsoWorkingForumBundle','attr' => ['class' => 'wf_button']])
                
                ;
                   
       
            
            
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
   /* public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Post'
        ));
    }*/

    /**
     * @return string
     */
    public function getName()
    {
        return 'yosimitso_working_forumbundle_post';
    }
}