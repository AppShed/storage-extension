<?php

namespace AppShed\Extensions\StorageBundle\Form;

use AppShed\Extensions\StorageBundle\Entity\Field;
use AppShed\Extensions\StorageBundle\Entity\Filter;
use AppShed\Extensions\StorageBundle\Entity\Store;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FieldType extends AbstractType
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
        $aggregate = [
            Field::AGGREGATE_COUNT,
            Field::AGGREGATE_AVG,
            Field::AGGREGATE_SUM,
            Field::AGGREGATE_MIN,
            Field::AGGREGATE_MAX
        ];
        $aggregate = array_combine ($aggregate, $aggregate);
        $fields = $this->store->getColumns();
        $fields = array_combine ($fields, $fields);

        $builder
            ->add('field', 'choice', [
                'choices' => $fields
            ])
            ->add('aggregate', 'choice', [
                'required' => false,
                'choices' => $aggregate,
                'empty_value' => '[none]'
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppShed\Extensions\StorageBundle\Entity\Field'
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
