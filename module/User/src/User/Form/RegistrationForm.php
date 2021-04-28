<?php

namespace User\Form;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

use User\Form\RegistrationFilter;

class RegistrationForm extends Form
{
  public function __construct(Adapter $dbAdapter, $countryMapper)
  {
    parent::__construct('registration');
    $this->setInputFilter(new RegistrationFilter($dbAdapter));
    $this->setAttribute('method', 'post');
    $this->setAttribute('enctype', 'multipart/form-data');
    $this->setHydrator(new ClassMethods());

    $this->add([
      'name' => 'first_name',
      'type' => 'text',
      'options' => [
        'label' => 'First Name *',
      ],
      'attributes' => [
        'class' => 'form-control form-control-user',
        'id' => 'first_name',
        'required' => 'required',
        'placeholder' => 'First Name',
      ],
    ]);

    $this->add([
      'name' => 'middle_name',
      'type' => 'text',
      'options' => [
        'label' => 'Middle Name *',
      ],
      'attributes' => [
        'class' => 'form-control form-control-user',
        'id' => 'middle_name',
        'required' => 'required',
        'placeholder' => 'Middle Name',
      ],
    ]);

    $this->add([
      'name' => 'last_name',
      'type' => 'text',
      'options' => [
        'label' => 'Last Name *',
      ],
      'attributes' => [
        'class' => 'form-control form-control-user',
        'id' => 'last_name',
        'required' => 'required',
        'placeholder' => 'Last Name',
      ],
    ]);

    $this->add([
      'name' => 'gender',
      'type' => 'Select',
      'options' => [
        'label' => 'Gender *',
      ],
      'attributes' => [
        'class' => 'form-control',
        'id' => 'gender',
        'required' => 'required',
        'options' => [
          '' => 'Select Gender',
          'm' => 'Male',
          'f' => 'Female',
        ],
      ],
    ]);

    $this->add([
      'name' => 'email',
      'type' => 'email',
      'options' => [
        'label' => 'Email Address *',
      ],
      'attributes' => [
        'class' => 'form-control form-control-user',
        'id' => 'email',
        'required' => 'required',
        'placeholder' => 'Email Address',
      ],
    ]);

    $this->add([
      'name' => 'password',
      'type' => 'password',
      'options' => [
        'label' => 'Password *',
      ],
      'attributes' => [
        'class' => 'form-control form-control-user',
        'id' => 'password',
        'required' => 'required',
        'placeholder' => 'Password',
      ],
    ]);

    $this->add([
      'name' => 'confirm_password',
      'type' => 'password',
      'options' => [
        'label' => 'Confirm password *',
      ],
      'attributes' => [
        'class' => 'form-control form-control-user',
        'id' => 'confirm_password',
        'required' => 'required',
        'placeholder' => 'Confirm Password',
      ],
    ]);

    $this->add([
      'name' => 'birth_date',
      'type' => 'text',
      'options' => [
        'label' => 'Birth Date *',
      ],
      'attributes' => [
        'class' => 'form-control form-control-user',
        'id' => 'birth_date',
        'required' => 'required',
        'placeholder' => 'Birth Date',
      ],
    ]);

    $this->add([
      'name' => 'title',
      'type' => 'text',
      'options' => [
        'label' => 'Title/Occupation',
      ],
      'attributes' => [
        'class' => 'form-control form-control-user',
        'id' => 'title',
        'placeholder' => 'Title/Occupation',
      ],
    ]);

    $this->add([
      'name' => 'city',
      'type' => 'text',
      'options' => [
        'label' => 'City',
      ],
      'attributes' => [
        'class' => 'form-control form-control-user',
        'id' => 'city',
        'placeholder' => 'City',
        'required' => 'required',
      ],
    ]);

    $this->add(array(
	    'name' => 'country_id',
	    'type' => 'Select',
	    'attributes' => array(
        'class' => 'form-control',
        'id' => 'country_id',
        'options' => $this->_getCountries($countryMapper),
        'required' => 'required',
	    ),
	    'options' => array(
        'label' => 'Country *',
	    ),
		));

    $this->add(array(
	    'name' => 'photo',
	    'attributes' => array(
        'type'  => 'file',
        'id' => 'photo',
				'aria-label' => 'Photo',
        'data-msg' => 'Please enter photo.',
        'data-error-class' => 'u-has-error',
        'data-success-class' => 'u-has-success',
	    ),
	    'options' => array(
        'label' => 'Photo',
				'label_attributes' => array(
          'class'  => 'form-label'
        ),
        'label_options' => array(
          'disable_html_escape' => true,
        ),
	    ),
		));

    $this->add([
      'name' => 'mobile_no',
      'type' => 'text',
      'options' => [
        'label' => 'Mobile No *',
      ],
      'attributes' => [
        'class' => 'form-control form-control-user',
        'id' => 'mobile_no',
        'required' => 'required',
        'placeholder' => 'Mobile No',
      ],
    ]);

    $this->add(array(
			'name' => 'security',
			'type' => 'Csrf',
		));

		$this->add(array(
			'name' => 'captcha',
			'type' => 'Captcha',
			'attributes' => array(
				'class' => 'form-control form-control-user',
				'id' => 'captcha',
				'placeholder'  => 'Please verify you are human.',
				'required' => 'required',
			),
			'options' => array(
				'label' => 'Security Code *',
				'captcha' => array(
					'class' => 'Dumb',
	        'wordLen' => 3,
				),
			),
		));

    $this->add([
      'name' => 'submit',
      'type' => 'submit',
      'attributes' => [
        'value' => 'Register Account',
        'class' => 'btn btn-primary btn-user btn-block',
      ],
    ]);
  }

  private function _getCountries($countryMapper){
    $countries = array(
      '' => 'Select Country',
    );
    $filter = array();
    $order = array(
      'country_name',
    );
    $temp = $countryMapper->fetch(false, $filter, $order);
    foreach ($temp as $country){
      $countries[$country->getId()] = $country->getCountryName();
    }

    return $countries;
	}
}
