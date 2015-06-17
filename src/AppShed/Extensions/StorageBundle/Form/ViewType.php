<?php

namespace AppShed\Extensions\StorageBundle\Form;

use AppShed\Extensions\StorageBundle\Entity\App;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ViewType extends AbstractType
{
    /**
     * @var \AppShed\Extensions\StorageBundle\Entity\App
     */
    protected $app;

    public function __construct(App $app)
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
            ->add('title', null, [
                'label' => 'Screen title'
            ])
            ->add('message', null, [
                'label' => 'Screen message'
            ])
            ->add('store', 'entity', [
                'class' => 'AppShed\Extensions\StorageBundle\Entity\Store',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('s')->andWhere('s.app = :a')->setParameter('a', $this->app);
                },
                'property' => 'name',
                'label' => 'Table'
            ])
            ->add('save', 'submit')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppShed\Extensions\StorageBundle\Entity\View',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appshed_extensions_storagebundle_view';
    }
}
