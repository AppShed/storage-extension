<?php

namespace AppShed\Extensions\StorageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('col')
            ->add('type', 'choice', [
                    'choices' => [
                        '>',
                        '<'
                    ]
                ])
            ->add('value')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppShed\Extensions\StorageBundle\Entity\Filter'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appshed_extensions_storagebundle_filter';
    }
}
