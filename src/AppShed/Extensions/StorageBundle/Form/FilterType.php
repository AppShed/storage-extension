<?php

namespace AppShed\Extensions\StorageBundle\Form;

use AppShed\Extensions\StorageBundle\Entity\Filter;
use AppShed\Extensions\StorageBundle\Entity\Store;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterType extends AbstractType
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
            ->add('col', 'choice', [
                'choices' => $this->arrayWithKeys($this->store->getColumns())
            ])
            ->add('type', 'choice', [
                    'choices' => $this->arrayWithKeys([
                        Filter::FILTER_EQUALS,
                        Filter::FILTER_GREATER_THAN,
                        Filter::FILTER_GREATER_THAN_OR_EQUALS,
                        Filter::FILTER_LESS_THAN,
                        Filter::FILTER_LESS_THAN_OR_EQUALS
                    ])
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

    private function arrayWithKeys($array) {
        return array_combine($array, $array);
    }
}
