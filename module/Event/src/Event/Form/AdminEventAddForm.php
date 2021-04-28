<?php

namespace Event\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

use Event\Form\AdminEventAddFilter;

class AdminEventAddForm extends Form
{
    public function __construct(Adapter $dbAdapter, $categoryMapper)
    {
        parent::__construct('admin-event-add');
        $this->setInputFilter(new AdminEventAddFilter($dbAdapter));
        $this->setAttribute('method', 'post');
        $this->setHydrator(new ClassMethods());
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add([
            'name' => 'name',
            'type' => 'text',
            'options' => [
                'label' => 'Name',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'name',
                'required' => 'required',
                'data-validation-required-message' => 'Please enter your name.',
                'autofocus' => 'autofocus',
                'placeholder' => 'Name',
            ],
        ]);

        $this->add([
            'name' => 'event_date',
            'type' => 'text',
            'options' => [
                'label' => 'Event Date',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'event_date',
                'required' => 'required',
                'data-validation-required-message' => 'Please enter your event date.',
                'placeholder' => 'Event Date',
            ],
        ]);

        $this->add([
            'name' => 'venue',
            'type' => 'text',
            'options' => [
                'label' => 'Venue',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'name',
                'required' => 'required',
                'data-validation-required-message' => 'Please enter venue.',
                'autofocus' => 'autofocus',
                'placeholder' => 'Venue',
            ],
        ]);

        $this->add([
            'name' => 'city',
            'type' => 'text',
            'options' => [
                'label' => 'City',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'city',
                'required' => 'required',
                'data-validation-required-message' => 'Please enter city.',
                'autofocus' => 'autofocus',
                'placeholder' => 'City',
            ],
        ]);

        $this->add([
            'name' => 'category_id',
            'type' => 'Select',
            'attributes' => [
                'class' => 'form-control',
                'id' => 'category_id',
                'options' => $this->_getCategories($categoryMapper),
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Category',
            ],
        ]);

        $this->add([
            'name' => 'description',
            'type' => 'textarea',
            'options' => [
                'label' => 'Description',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'description',
                'required' => 'required',
                'data-validation-required-message' => 'Please enter your description address.',
                'placeholder' => 'Description',
            ],
        ]);

        $this->add([
            'name' => 'organization',
            'type' => 'text',
            'options' => [
                'label' => 'Organization',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'organization',
                'data-validation-required-message' => 'Please enter organization.',
                'placeholder' => 'Organization',
            ],
        ]);

        $this->add([
            'name' => 'contact_name',
            'type' => 'text',
            'options' => [
                'label' => 'Contact Name',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'contact_name',
                'data-validation-required-message' => 'Please enter contact name.',
                'placeholder' => 'Contact Name',
            ],
        ]);

        $this->add([
            'name' => 'contact_email',
            'type' => 'email',
            'options' => [
                'label' => 'Contact Email',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'contact_email',
                'data-validation-required-message' => 'Please enter contact email.',
                'placeholder' => 'Contact Email',
            ],
        ]);

        $this->add([
            'name' => 'contact_mobile_no',
            'type' => 'text',
            'options' => [
                'label' => 'Contact Mobile No.',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'contact_mobile_no',
                'data-validation-required-message' => 'Please enter contact mobile no.',
                'placeholder' => 'Contact Mobile No.',
            ],
        ]);

        $this->add([
            'name' => 'website_url',
            'type' => 'text',
            'options' => [
                'label' => 'Website URL',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'website_url',
                'data-validation-required-message' => 'Please enter website URL.',
                'placeholder' => 'Website URL',
            ],
        ]);

        $this->add([
            'name' => 'photo',
            'attributes' => [
                'type'  => 'file',
                'id' => 'photo',
                'aria-label' => 'Photo',
                'data-msg' => 'Please enter photo.',
                'data-error-class' => 'u-has-error',
                'data-success-class' => 'u-has-success',
            ],
            'options' => [
                'label' => 'Photo',
            ],
        ]);

        $this->add([
            'name' => 'event_type',
            'type' => 'Select',
            'attributes' => [
                'class' => 'form-control',
                'id' => 'event_type',
                'options' => [
                    'individual' => 'Individual',
                    'team' => 'Team',
                ],
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Event Type',
            ],
        ]);

        $this->add([
            'name' => 'volunteer_limit',
            'type' => 'text',
            'options' => [
                'label' => 'Volunteer Limit',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'volunteer_limit',
                'autofocus' => 'autofocus',
                'placeholder' => 'Volunteer Limit',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Submit',
                'class' => 'btn btn-primary',
            ],
        ]);
    }

    private function _getCategories($categoryMapper)
    {
        $temp = ['' => 'Select Category'];

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
