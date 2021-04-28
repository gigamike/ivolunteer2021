<?php

namespace Event\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;

use Event\Form\SearchEventFilter;

class EventSearchForm extends Form
{
    public function __construct(Adapter $dbAdapter, $categoryMapper)
    {
        parent::__construct('search-event');
        $this->setInputFilter(new EventSearchFilter($dbAdapter));
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'name_keyword',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => '%Event Name%',
            ],
        ]);

        $this->add([
            'name' => 'organization_keyword',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => '%Organization%',
            ],
        ]);

        $this->add([
            'name' => 'category_id',
            'type' => 'Select',
            'attributes' => [
                'class' => 'form-control',
                'id' => 'category_id',
                'options' => $this->_getCategories($categoryMapper),
            ],
            'options' => [
                'label' => 'Category',
            ],
        ]);

        $this->add([
            'name' => 'event_date',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control datepicker',
                'placeholder' => 'Event Date',
            ],
        ]);

        $this->add([
            'name' => 'distance',
            'type' => 'Select',
            'attributes' => [
                'class' => 'form-control',
                'id' => 'distance',
                'options' => [
                    '' => 'All',
                    '6' => '6 KM',
                    '10' => '10 KM',
                    '20' => '20 KM.',
                    '30' => '30 KM.',
                    '40' => '40 KM.',
                    '50' => '50 KM.',
                ],
            ],
            'options' => [
                'label' => 'Distance',
            ],
        ]);

        $this->add([
            'name' => 'city_keyword',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => '%City%',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Search',
                'class' => 'btn btn-primary',
            ],
        ]);
    }

    private function _getCategories($categoryMapper)
    {
        $temp = ['' => 'All Category/Cause'];

        $filter = [];
        $order = [
            'category',
        ];
        $categories = $categoryMapper->fetch(false, $filter, $order);
        foreach ($categories as $row) {
            $temp[$row->getId()] = $row->getCategory();
        }

        return $temp;
    }
}
