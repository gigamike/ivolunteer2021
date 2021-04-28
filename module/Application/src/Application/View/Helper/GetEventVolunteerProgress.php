<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetEventVolunteerProgress extends AbstractHelper
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

    public function getEventMapper()
    {
        return $this->getSm()->get('EventMapper');
    }

    public function getUserEventMapper()
    {
        return $this->getSm()->get('UserEventMapper');
    }

    public function getEventTeamMapper()
    {
        return $this->getSm()->get('EventTeamMapper');
    }

    public function __invoke($id)
    {
        $html = "";

        $event = $this->getEventMapper()->getEvent($id);
        if ($event) {
            switch ($event->getEventType()) {
                case 'individual':
                    $filter = [
                        'event_id' => $event->getId(),
                    ];
                    $volunteers = $this->getUserEventMapper()->getCount($filter);
                    $volunteers['count_id'] = isset($volunteers['count_id']) ? $volunteers['count_id'] : 0;
                    
                    $percent = 0;
                    if ($event->getVolunteerLimit() != 0) {
                        $percent = round(($volunteers['count_id'] / $event->getVolunteerLimit()) * 100, 2);
                    }
                   
                    $html = "<small>Goal: " . $event->getVolunteerLimit() . "</small>";
                    $html .= "<div class=\"progress\"><div class=\"progress-bar\" role=\"progressbar\" style=\"width: " . $percent . "%\" aria-valuenow=\"" . $percent . "\" aria-valuemin=\"0\" aria-valuemax=\"100\">" . $percent . "%</div></div>";

                    $remaining = $event->getVolunteerLimit() - $volunteers['count_id'];
                    if ($remaining > 0) {
                        $html .= "<small>" . $remaining . " more volunteers needed</small>";
                    } else {
                        $html .= "<small>Full</small>";
                    }
                    
                    $html .= "<br>";
                    break;
                case 'team':
                    $filter = [
                        'event_id' => $event->getId(),
                    ];
                    $volunteers = $this->getUserEventMapper()->getCount($filter);
                    $volunteers['count_id'] = isset($volunteers['count_id']) ? $volunteers['count_id'] : 0;

                    $teamLimit = $this->getEventTeamMapper()->getSumTeamLimit($filter);
                    $teamLimit['sum_team_limit'] = isset($teamLimit['sum_team_limit']) ? $teamLimit['sum_team_limit'] : 0;

                    $percent = 0;
                    if ($teamLimit['sum_team_limit'] != 0) {
                        $percent = round(($volunteers['count_id'] / $teamLimit['sum_team_limit']) * 100, 2);
                    }
                   
                    $html = "<small>Goal: " . $teamLimit['sum_team_limit'] . "</small>";
                    $html .= "<div class=\"progress\"><div class=\"progress-bar\" role=\"progressbar\" style=\"width: " . $percent . "%\" aria-valuenow=\"" . $percent . "\" aria-valuemin=\"0\" aria-valuemax=\"100\">" . $percent . "%</div></div>";

                    $remaining = $teamLimit['sum_team_limit'] - $volunteers['count_id'];
                    if ($remaining > 0) {
                        $html .= "<small>" . $remaining . " more volunteers needed</small>";
                    } else {
                        $html .= "<small>Full</small>";
                    }
                    
                    $html .= "<br>";
                    break;
                default:
                    break;
            }
        }
        
        return $html;
    }
}
