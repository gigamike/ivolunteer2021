<?php
namespace Event\Model;

class EventTeamMemberEntity
{
    protected $id;
    protected $event_team_id;
    protected $user_id;
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

    public function getEventTeamId()
    {
        return $this->event_team_id;
    }

    public function setEventTeamId($value)
    {
        $this->event_team_id = $value;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($value)
    {
        $this->user_id = $value;
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
