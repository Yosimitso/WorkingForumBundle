<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminSubforumType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('name','text',['error_bubbling' => true, 'attr' => ['class' => 'form_subforum']])
                ->add('nbThread','number',['disabled' => true,'attr' => ['style' => 'width:30px']])
                ->add('nbPost','number',['disabled' => true,'attr' => ['style' => 'width:30px']])
           
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Subforum'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yosimitso_workingforumbundle_subforum';
    }
}
