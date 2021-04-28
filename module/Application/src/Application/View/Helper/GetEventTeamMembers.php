<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetEventTeamMembers extends AbstractHelper
{
    private $_sm;

    public function __construct(\Zend\ServiceManager\ServiceManager $sm)
    {
        $this->_sm = $sm;
    }

    public function getSm()
    {
        return $this->_sm;
    }

    public function getEventTeamMapper()
    {
        return $this->getSm()->get('EventTeamMapper');
    }

    public function getEventTeamMemberMapper()
    {
        return $this->getSm()->get('EventTeamMemberMapper');
    }

    public function __invoke($id)
    {
        $html = "";
  
        $eventTeam = $this->getEventTeamMapper()->getEventTeam($id);
        if ($eventTeam) {
            $filter = [
                'event_team_id' => $eventTeam->getId(),
            ];
            $order = [
                'user.first_name',
                'user.last_name',
            ];
            $members = $this->getEventTeamMemberMapper()->getTeamMembers(false, $filter, $order);

            if (count($members) > 0) {
                $html .= "<ul>";
                foreach ($members as $member) {
                    $html .= "<li>";
                    $html .=  $member['first_name'] . " " . $member['last_name'];
                    $html .= "</li>";
                }
                $html .= "</ul>";
            } else {
                $html .= "No members yet";
            }
        }
        
        return $html;
    }
}
