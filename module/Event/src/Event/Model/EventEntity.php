<?php
namespace Event\Model;

class EventEntity
{
    protected $id;
    protected $category_id;
    protected $name;
    protected $description;
    protected $venue;
    protected $city;
    protected $latitude;
    protected $longitude;
    protected $event_date;
    protected $event_type;
    protected $volunteer_limit;
    protected $organization;
    protected $contact_name;
    protected $contact_email;
    protected $contact_mobile_no;
    protected $website_url;
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

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function setCategoryId($value)
    {
        $this->category_id = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getVenue()
    {
        return $this->venue;
    }

    public function setVenue($value)
    {
        $this->venue = $value;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($value)
    {
        $this->city = $value;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($value)
    {
        $this->latitude = $value;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($value)
    {
        $this->longitude = $value;
    }

    public function getEventDate()
    {
        return $this->event_date;
    }

    public function setEventDate($value)
    {
        $this->event_date = $value;
    }

    public function getEventType()
    {
        return $this->event_type;
    }

    public function setEventType($value)
    {
        $this->event_type = $value;
    }

    public function getVolunteerLimit()
    {
        return $this->volunteer_limit;
    }

    public function setVolunteerLimit($value)
    {
        $this->volunteer_limit = $value;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    public function setOrganization($value)
    {
        $this->organization = $value;
    }

    public function getContactName()
    {
        return $this->contact_name;
    }

    public function setContactName($value)
    {
        $this->contact_name = $value;
    }

    public function getContactEmail()
    {
        return $this->contact_email;
    }

    public function setContactEmail($value)
    {
        $this->contact_email = $value;
    }

    public function getContactMobileNo()
    {
        return $this->contact_mobile_no;
    }

    public function setContactMobileNo($value)
    {
        $this->contact_mobile_no = $value;
    }

    public function getWebsiteUrl()
    {
        return $this->website_url;
    }

    public function setWebsiteUrl($value)
    {
        $this->website_url = $value;
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
