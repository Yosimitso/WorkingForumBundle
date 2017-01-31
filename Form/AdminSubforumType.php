<?php

namespace Yosimitso\WorkingForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\CallbackTransformer;

class AdminSubforumType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'error_bubbling' => true,
                    'attr'           => ['class' => 'form_subforum'],
                ]
            )
            ->add(
                'nbThread',
                NumberType::class,
                [
                    'disabled' => true,
                    'attr'     => ['style' => 'width:30px'],
                ]
            )
            ->add(
                'nbPost',
                NumberType::class,
                [
                    'disabled' => true,
                    'attr'     => ['style' => 'width:30px'],
                ]
            )
                ->add('allowedRoles',TextType::class,['error_bubbling' => true, 'required' => false])
                ->get('allowedRoles')
                    ->addModelTransformer(new CallbackTransformer (
                        function ($rolesAsArray) {
                            if (isset($rolesAsArray) && is_array($rolesAsArray))
                            {
                                return implode(',',(array) $rolesAsArray);
                            }
                            else
                            {
                                return '';
                            }
                        },
                        function ($rolesAsString) {
                            return explode(',',str_replace(' ','',$rolesAsString));
                        }
                        ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Yosimitso\WorkingForumBundle\Entity\Subforum',
            ]
        );
    }


}
