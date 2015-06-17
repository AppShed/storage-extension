<?php

namespace AppShed\Extensions\StorageBundle\Form;

use AppShed\Extensions\StorageBundle\Entity\Store;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FiltersViewType extends AbstractType
{
    /**
     * @var \AppShed\Extensions\StorageBundle\Entity\Store
     */
    private $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filters', 'collection', [
                'type' => new FilterType($this->store),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
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
