<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetEventTeamProgress extends AbstractHelper
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
            $volunteers = $this->getEventTeamMemberMapper()->getCount($filter);
            $volunteers['count_id'] = isset($volunteers['count_id']) ? $volunteers['count_id'] : 0;

            $percent = 0;
            if ($eventTeam->getTeamLimit() == 0) {
                $percent = round(($volunteers['count_id'] / $eventTeam->getTeamLimit()) * 100, 2);
            }
                   
            $html = "<small>Goal: " . $eventTeam->getTeamLimit() . "</small>";
            $html .= "<div class=\"progress\"><div class=\"progress-bar\" role=\"progressbar\" style=\"width: " . $percent . "%\" aria-valuenow=\"" . $percent . "\" aria-valuemin=\"0\" aria-valuemax=\"100\">" . $percent . "%</div></div>";

            $remaining = $eventTeam->getTeamLimit() - $volunteers['count_id'];
            if ($remaining > 0) {
                $html .= "<small>" . $remaining . " more volunteers needed for " . $eventTeam->getName() . "</small>";
            } else {
                $html .= "<small>Full</small>";
            }
                    
            $html .= "<br>";
        }
        
        return $html;
    }
}
