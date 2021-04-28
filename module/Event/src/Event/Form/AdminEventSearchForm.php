<?php

namespace Event\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;

use Event\Form\AdminSearchEventFilter;

class AdminEventSearchForm extends Form
{
    public function __construct(Adapter $dbAdapter)
    {
        parent::__construct('admin-search-event');
        $this->setInputFilter(new AdminEventSearchFilter($dbAdapter));
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'name_keyword',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => '%Name%',
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
}
