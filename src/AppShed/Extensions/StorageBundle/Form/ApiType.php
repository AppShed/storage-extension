<?php

namespace AppShed\Extensions\StorageBundle\Form;

use AppShed\Extensions\StorageBundle\Entity\Api;
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
        $action = [
            Api::ACTION_SELECT,
            Api::ACTION_INSERT,
            Api::ACTION_UPDATE,
            Api::ACTION_DELETE
        ];
        $action = array_combine ($action, $action);

        $builder
            ->add('name')
            ->add('action', 'choice', [
                'choices' => $action
            ])
            ->add('store', 'entity', [
                'class' => 'AppShedExtensionsStorageBundle:Store',
                'query_builder' => function(EntityRepository $er, $options = '') {
                    return $er->createQueryBuilder('s')
                        ->where('s.app = :app')
                        ->setParameter('app', $this->app)
                        ->orderBy('s.name', 'ASC');
                }
            ])

            ->add('next', 'submit', [
                'attr' => [
                    'class' => 'btn-submit-float'
                ]
            ])
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
