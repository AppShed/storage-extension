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
            ->add('store', 'entity', [
                'class' => 'AppShed\Extensions\StorageBundle\Entity\Store',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('s')->andWhere('s.app = :a')->setParameter('a', $this->app);
                },
                'property' => 'name'
            ])
            ->add('submit', 'submit')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppShed\Extensions\StorageBundle\Entity\View'
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
