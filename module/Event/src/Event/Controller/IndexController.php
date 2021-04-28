<?php

namespace Event\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message as Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime as MimeMime;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Sendmail as Sendmail;

use Dompdf\Dompdf;
use Dompdf\Options;

use GeoIp2\Database\Reader;

use User\Model\UserEventEntity;
use Event\Model\EventTeamMemberEntity;

class IndexController extends AbstractActionController
{
    public function getEventMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('EventMapper');
    }

    public function getUserEventMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('UserEventMapper');
    }

    public function getUserMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('UserMapper');
    }

    public function getEventTeamMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('EventTeamMapper');
    }

    public function getEventTaskMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('EventTaskMapper');
    }

    public function getEventTeamMemberMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('EventTeamMemberMapper');
    }

    private function _getIP()
    {
        $config = $this->getServiceLocator()->get('Config');


        /*
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
            $_SERVER['REMOTE_ADDR'] = $config['ip'];
        }
        */
       
        $_SERVER['REMOTE_ADDR'] = $config['ip'];
    }

    public function indexAction()
    {
        $route = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

        $this->_getIP();
        // echo $_SERVER['REMOTE_ADDR'];
      
        $latitude = null;
        $longitude = null;
        $city = null;
        $countryCode = null;
        $country = null;
        if ($_SERVER['REMOTE_ADDR']) {
            $basePath = dirname($_SERVER['DOCUMENT_ROOT']);
            $reader = new Reader($basePath . "/geoip/GeoLite2-City.mmdb");

            $record = $reader->city($_SERVER['REMOTE_ADDR']);

            $latitude = $record->location->latitude;
            $longitude = $record->location->longitude;
            $countryCode = $record->country->isoCode;
            $city = $record->city->name;
            $country = $record->country->name;

            /*
            echo "Your IP: " . $_SERVER['REMOTE_ADDR'] . "<br>";
            echo "Your Country Code: " . $record->country->isoCode . "<br>";
            echo "Your Country Name: " . $record->country->name . "<br>";
            echo "Your latitude: " . $record->location->latitude . "<br>";
            echo "Your longitude " . $record->location->longitude . "<br>";
            echo "Your City Name: " . $record->city->name . "<br>";
            */
        }

        $searchFilter = [];
        if (!empty($search_by)) {
            $searchFilter = (array) json_decode($search_by);
        }

        $searchFilter['latitude'] = $latitude;
        $searchFilter['longitude'] = $longitude;

        if (!isset($searchFilter['distance'])) {
            //$searchFilter['distance'] = 6;
        }

        $form = $this->getServiceLocator()->get('EventSearchForm');
        $form->setData($searchFilter);

        $order = ['distance', 'event_date DESC'];
        $paginator = $this->getEventMapper()->getEvents(true, $searchFilter, $order);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(12);

        return new ViewModel([
            'form' => $form,
            'paginator' => $paginator,
            'search_by' => $search_by,
            'page' => $page,
            'searchFilter' => $searchFilter,
            'route' => $route,
            'city' => $city,
        ]);
    }

    public function searchAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formdata = (array) $request->getPost();
            $search_data = [];
            foreach ($formdata as $key => $value) {
                if ($key != 'submit') {
                    if (!empty($value)) {
                        $search_data[$key] = $value;
                    }
                }
            }

            if (!empty($search_data)) {
                $search_by = json_encode($search_data);
                return $this->redirect()->toRoute('events', ['search_by' => $search_by]);
            } else {
                return $this->redirect()->toRoute('events');
            }
        } else {
            return $this->redirect()->toRoute('events');
        }
    }

    public function joinAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }

        $event = $this->getEventMapper()->getEvent($id);
        if (!$event) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
            return $this->redirect()->toRoute('home');
        }

        $filter = [
            'event_id' => $event->getId(),
        ];
        $order = [
            'id',
        ];
        $tasks = $this->getEventTaskMapper()->fetch(false, $filter, $order);

        $teams = [];
        switch ($event->getEventType()) {
            case 'individual':
                break;
            case 'team':
                $filter = [
                    'event_id' => $event->getId(),
                ];
                $order = [
                    'name',
                ];
                $teams = $this->getEventTeamMapper()->fetch(false, $filter, $order);
                break;
            default:
                break;
        }

        $authService = $this->serviceLocator->get('auth_service');
        if (!$authService->getIdentity()) {
            $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
            if (isset($_SERVER['REQUEST_URI'])) {
                if (substr($_SERVER['REQUEST_URI'], 0, 1) == "/") {
                    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 1);
                }
                $config = $this->getServiceLocator()->get('Config');
                $redirectUrl = "/login?redirectUrl=" . $config['baseUrl'] . $_SERVER['REQUEST_URI'];
                header("Location: " . $redirectUrl);
                exit();
            } else {
                return $this->redirect()->toRoute('login');
            }
        }

        $user_id = $authService->getIdentity()->id;
        $user = $this->getUserMapper()->getUser($user_id);
        if (!$user) {
            $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
            if (isset($_SERVER['REQUEST_URI'])) {
                if (substr($_SERVER['REQUEST_URI'], 0, 1) == "/") {
                    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 1);
                }
                $config = $this->getServiceLocator()->get('Config');
                $redirectUrl = "/login?redirectUrl=" . $config['baseUrl'] . $_SERVER['REQUEST_URI'];
                $this->redirect()->toUrl($redirectUrl);
            } else {
                return $this->redirect()->toRoute('login');
            }
        }

        $userEvent = $this->getUserEventMapper()->getUserEventByUserIdAndEventId($user_id, $event->getId());

        $eventTeamMember = null;
        $teamStandings = null;
        switch ($event->getEventType()) {
            case 'individual':
                break;
            case 'team':
                $filter = [
                    'event_id' => $event->getId(),
                    'user_id' => $user_id,
                ];
                $eventTeamMember = $this->getEventTeamMemberMapper()->getEventTeamMembers(false, $filter, [], 1);

                $filter = [
                    'event_id' => $event->getId(),
                ];
                $order = [
                    'points DESC',
                ];
                $teamStandings = $this->getEventTeamMapper()->getTeamStandings(false, $filter, $order);

                break;
            default:
                break;
        }

        $filter = [
            'id_not' => $id,
        ];
        $order = ['event_date DESC'];
        $previousEvents = $this->getEventMapper()->fetch(false, $filter, $order, 6);

        return new ViewModel([
            'event' => $event,
            'teams' => $teams,
            'tasks' => $tasks,
            'userEvent' => $userEvent,
            'user' => $user,
            'eventTeamMember' => $eventTeamMember,
            'previousEvents' => $previousEvents,
            'teamStandings' => $teamStandings,
        ]);
    }

    public function volunteerAction()
    {
        if ($this->getRequest()->isPost()) {
            $config = $this->getServiceLocator()->get('Config');

            $data = $this->params()->fromPost();

            if (!isset($data['event_id'])) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
                return $this->redirect()->toRoute('events');
            }
            
            $event = $this->getEventMapper()->getEvent($data['event_id']);
            if (!$event) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
                return $this->redirect()->toRoute('events');
            }
          
            switch ($event->getEventType()) {
                case 'individual':
                    break;
                case 'team':
                    if (empty($data['team_id'])) {
                        $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Team.');
                        return $this->redirect()->toRoute('events', ['action' => 'join', 'id' => $event->getId()]);
                    }

                    $eventTeam = $this->getEventTeamMapper()->getEventTeam($data['team_id']);
                    if (!$eventTeam) {
                        $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Team.');
                        return $this->redirect()->toRoute('events');
                    }
                    break;
                default:
                    break;
            }

            $authService = $this->serviceLocator->get('auth_service');
            if (!$authService->getIdentity()) {
                $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
                return $this->redirect()->toRoute('login');
            }

            $user_id = $authService->getIdentity()->id;

            $user = $this->getUserMapper()->getUser($user_id);
            if (!$user) {
                $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
                return $this->redirect()->toRoute('login');
            }

            $filter = [
                'user_id' => $user_id,
                'event_id' => $event->getId(),
            ];
            $userEvent = $this->getUserEventMapper()->fetch(false, $filter);
            if (count($userEvent) > 0) {
                $this->flashMessenger()->setNamespace('error')->addMessage('You already join in this event.');
                return $this->redirect()->toRoute('events');
            }

            $userEvent = new UserEventEntity();
            $userEvent->setEventId($event->getId());
            $userEvent->setUserId($user_id);
            $userEvent->setCreatedUserId($user_id);
            $this->getUserEventMapper()->save($userEvent);

            switch ($event->getEventType()) {
                case 'individual':
                    break;
                case 'team':
                    $eventTeamMember = new EventTeamMemberEntity();
                    $eventTeamMember->setEventTeamId($eventTeam->getId());
                    $eventTeamMember->setUserId($user_id);
                    $eventTeamMember->setCreatedUserId($user_id);
                    $this->getEventTeamMemberMapper()->save($eventTeamMember);
                    break;
                default:
                    break;
            }

            try {
                $message ="Hi "  . $user->getFirstName() . "\n\n\nThank you for volunteering on " . $event->getName() . ". Please see attach ticket. If in case you dont have an internet during the event, please save a copy of the ticket or screenshot of your QR Code. See you there!\n\niVolunteer Team";

                $plainText = $message;
                $html = nl2br($plainText);

                $this->_generateEventPdf($event->getId());

                $content = new MimeMessage();
                $text = new MimePart($plainText);
                $text->type = "text/plain";
                $html = new MimePart($html);
                $html->type = "text/html";
                $content->setParts([$text, $html]);

                $contentPart = new MimePart($content->generateMessage());
                $contentPart->type = 'multipart/alternative;' . PHP_EOL . ' boundary="' . $content->getMime()->boundary() . '"';

                $fileContent = fopen(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . "/data/pdf/user/" . $user_id . ".pdf", 'r');
                $attachment = new MimePart($fileContent);
                $attachment->type = 'application/pdf';
                $attachment->filename = $user_id . ".pdf";
                $attachment->disposition = MimeMime::DISPOSITION_ATTACHMENT;
                // Setting the encoding is recommended for binary data
                $attachment->encoding = MimeMime::ENCODING_BASE64;

                $body = new MimeMessage();
                $body->setParts([$contentPart, $attachment]);

                $mail = new Message();
                $mail->setFrom($config['email']);
                $mail->addTo($user->getEmail());
                $mail->setSubject("Volunteer at " . $event->getName());
                $mail->setBody($body);

                // Setup SMTP transport using LOGIN authentication
                $transport = new SmtpTransport();
                $options   = new SmtpOptions([
                    'name'              => $config['smtp']['host'],
                    'host'              => $config['smtp']['host'],
                    'port'              => $config['smtp']['port'],
                    'connection_class'  => 'login',
                    'connection_config' => [
                        'username' => $config['smtp']['connection_config']['username'],
                        'password' => $config['smtp']['connection_config']['password'],
                    ],
                ]);
                $transport->setOptions($options);
                $transport->send($mail);
            } catch (\Exception $e) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Error in email.' . $e->message);
                return $this->redirect()->toRoute('events', ['action' => 'join', 'id' => $event->getId()]);
            }

            $this->flashMessenger()->setNamespace('success')->addMessage('You succesfully join in this event.');
            return $this->redirect()->toRoute('events', ['action' => 'join', 'id' => $event->getId()]);
        } else {
            return $this->redirect()->toRoute('events');
        }
    }

    private function _generateEventPdf($event_id)
    {
        $event = $this->getEventMapper()->getEvent($event_id);
        if ($event) {
            $authService = $this->serviceLocator->get('auth_service');
            $user_id = $authService->getIdentity()->id;
            $user = $this->getUserMapper()->getUser($user_id);
            if ($user) {
                $pdfFile = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . "/data/pdf/user/" . $user_id . ".pdf";
                if (file_exists($pdfFile)) {
                    unlink($pdfFile);
                }

                $html = "";
                $qr = "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=mik&choe=UTF-8";
                $html .= "<table>
<tbody>
<tr>
    <td><img src=\"" . $qr . "\"></td>
    <td>
        <table>
            <tr>
                <td><img src=\"https://www.gigamike.net/img/ivolunteerLogo.png\" style=\"height:80px\"></td>
            </tr>
            <tr>
                <td><h1>iVolunteer PH</h1></td>
            </tr>
            <tr>
                <td>Event: " . $event->getName() . "</td>
            </tr>
            <tr>
                <td>Event Date:" . $event->getEventDate() . " </td>
            </tr>
            <tr>
                <td>Event Venue: " . $event->getVenue() . " </td>
            </tr>
             <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Volunteer Name: " . $user->getFirstName() . " " . $user->getLastName() . "</td>
            </tr>
            <tr>
                <td>Volunteer Email: " . $user->getEmail() . "</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>
    </td>
</tr>
</tbody>
</table>";
        

                // instantiate and use the dompdf class
        
                $options = new Options();
                $options->setIsRemoteEnabled(true);
                $options->setIsHtml5ParserEnabled(true);

                $dompdf = new Dompdf();
                $dompdf->setOptions($options);
                $dompdf->loadHtml($html);
                // $dompdf->set_option('isRemoteEnabled', true);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                $output = $dompdf->output();
                file_put_contents($pdfFile, $output);
            }
        }
    }

    public function pdfAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }

        $event = $this->getEventMapper()->getEvent($id);
        if (!$event) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
            return $this->redirect()->toRoute('home');
        }

        $authService = $this->serviceLocator->get('auth_service');
        $user_id = $authService->getIdentity()->id;
        $user = $this->getUserMapper()->getUser($user_id);
        if (!$user) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid User.');
            return $this->redirect()->toRoute('home');
        }

        $html = "";
        $qr = "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=mik&choe=UTF-8";
        $html .= "<table>
<tbody>
<tr>
    <td><img src=\"" . $qr . "\"></td>
    <td>
        <table>
            <tr>
                <td><img src=\"https://www.gigamike.net/img/ivolunteerLogo.png\" style=\"height:80px\"></td>
            </tr>
            <tr>
                <td><h1>iVolunteer PH</h1></td>
            </tr>
            <tr>
                <td>Event: " . $event->getName() . "</td>
            </tr>
            <tr>
                <td>Event Date:" . $event->getEventDate() . " </td>
            </tr>
            <tr>
                <td>Event Venue: " . $event->getVenue() . " </td>
            </tr>
             <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Volunteer Name: " . $user->getFirstName() . " " . $user->getLastName() . "</td>
            </tr>
            <tr>
                <td>Volunteer Email: " . $user->getEmail() . "</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>
    </td>
</tr>
</tbody>
</table>";
        

        // instantiate and use the dompdf class
        
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);

        $dompdf = new Dompdf();
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        // $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
    }
}
