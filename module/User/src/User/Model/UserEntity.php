<?php
namespace User\Model;

class UserEntity
{
    protected $id;
    protected $role;
    protected $email;
    protected $password;
    protected $first_name;
    protected $middle_name;
    protected $last_name;
    protected $gender;
    protected $city;
    protected $country_id;
    protected $birth_date;
    protected $title;
    protected $mobile_no;
    protected $salt;
    protected $active;
    protected $referral_id;
    protected $created_datetime;
    protected $created_user_id;
    protected $modified_datetime;
    protected $modified_user_id;

    public function __construct()
    {
        $this->created_datetime = date('Y-m-d H:i:s');
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($value)
    {
        $this->role = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($value)
    {
        $this->password = $value;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function setFirstName($value)
    {
        $this->first_name = $value;
    }

    public function getMiddleName()
    {
        return $this->middle_name;
    }

    public function setMiddleName($value)
    {
        $this->middle_name = $value;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function setLastName($value)
    {
        $this->last_name = $value;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($value)
    {
        $this->gender = $value;
    }

    public function setCity($value)
    {
        $this->city = $value;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCountryId($value)
    {
        $this->country_id = $value;
    }

    public function getCountryId()
    {
        return $this->country_id;
    }

    public function getBirthDate()
    {
        return $this->birth_date;
    }

    public function setBirthDate($value)
    {
        $this->birth_date = $value;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($value)
    {
        $this->title = $value;
    }

    public function getMobileNo()
    {
        return $this->mobile_no;
    }

    public function setMobileNo($value)
    {
        $this->mobile_no = $value;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($value)
    {
        $this->salt = $value;
    }

    public function setActive($value)
    {
        $this->active = $value;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setReferralId($value)
    {
        $this->referral_id = $value;
    }

    public function getReferralId()
    {
        return $this->referral_id;
    }

    public function getCreatedDatetime()
    {
        return $this->created_datetime;
    }

    public function setCreatedDatetime($value)
    {
        $this->created_datetime = $value;
    }

    public function getCreatedUserId()
    {
        return $this->created_user_id;
    }

    public function setCreatedUserId($value)
    {
        $this->created_user_id = $value;
    }

    public function getModifiedDatetime()
    {
        return $this->modified_datetime;
    }

    public function setModifiedDatetime($value)
    {
        $this->modified_datetime = $value;
    }

    public function getModifiedUserId()
    {
        return $this->modified_user_id;
    }

    public function setModifiedUserId($value)
    {
        $this->modified_user_id = $value;
    }
}
