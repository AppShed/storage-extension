<?php

namespace AppShed\Extensions\StorageBundle\Form;

use AppShed\Extensions\StorageBundle\Entity\Api;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApiEditType extends AbstractType
{
    protected $app;

    public function __construct(Api $api = null)
    {
        $this->api = $api;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fields = $this->api->getStore()->getColumns();
        $fields = array_combine ($fields, $fields);

        switch ($this->api->getAction()) {
            case Api::ACTION_SELECT: {
                $builder
                    ->add('fields', 'collection', [
                        'type' => new FieldType($this->api->getStore()),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false
                    ])
                    ->add('filters', 'collection', [
                        'type' => new FilterType($this->api->getStore()),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false
                    ])
                    ->add('groupField', 'choice', [
                        'required' => false,
                        'choices' => $fields
                    ])
                    ->add('orderField', 'choice', [
                        'required' => false,
                        'choices' => $fields
                    ])
                    ->add('orderDirection', 'choice', [
                        'required' => false,
                        'choices' => [
                            Api::ODRER_DIRECTION_ASC => Api::ODRER_DIRECTION_ASC,
                            Api::ODRER_DIRECTION_DESC => Api::ODRER_DIRECTION_DESC
                        ]
                    ])
                    ->add('limit');
            } break;
            case Api::ACTION_UPDATE:
            case Api::ACTION_DELETE: {
            $builder
                ->add('filters', 'collection', [
                    'type' => new FilterType($this->api->getStore()),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false
                ])
                ->add('limit');
            } break;
            case Api::ACTION_INSERT: {
                //nothing add
            } break;
        }
        $builder
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppShed\Extensions\StorageBundle\Entity\Api',
            'csrf_protection' => false
        ));

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appshed_extensions_storagebundle_api_edit';
    }
}
