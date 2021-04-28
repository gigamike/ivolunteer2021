<?php
namespace Event\Model;

class EventTeamEntity
{
    protected $id;
    protected $event_id;
    protected $name;
    protected $team_limit;
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

    public function getEventId()
    {
        return $this->event_id;
    }

    public function setEventId($value)
    {
        $this->event_id = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getTeamLimit()
    {
        return $this->team_limit;
    }

    public function setTeamLimit($value)
    {
        $this->team_limit = $value;
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
