<?php

namespace AppShed\Extensions\StorageBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApiType extends AbstractType
{
    protected $app;

    public function __construct($app = null)
    {
        $this->app = $app;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name')
            ->add('action', 'choice', [
                'choices' => [
                    'Select' => 'Select',
                    'Insert' => 'Insert',
                    'Update' => 'Update',
                    'Delete' => 'Delete',
                ]
            ])
            ->add('store', 'entity', array(
                    'class' => 'AppShedExtensionsStorageBundle:Store',
                    'query_builder' => function(EntityRepository $er, $options = '') {
                        return $er->createQueryBuilder('s')
                            ->where('s.app = :app')
                            ->setParameter('app', $this->app)
                            ->orderBy('s.name', 'ASC');
                    },)
            )

            ->add('next', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppShed\Extensions\StorageBundle\Entity\Api'
        ));

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appshed_extensions_storagebundle_api';
    }
}
