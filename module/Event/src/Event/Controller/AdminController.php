<?php

namespace Event\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Gumlet\ImageResize;

use Event\Form\AdminEventEditForm;
use Event\Model\EventEntity;
use Event\Model\EventTeamEntity;
use Event\Model\EventTaskEntity;
use User\Model\UserEventTaskEntity;

class AdminController extends AbstractActionController
{
    public function getEventMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('EventMapper');
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

    public function getUserEventTaskMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('UserEventTaskMapper');
    }

    public function getCategoryMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('CategoryMapper');
    }

    private function getLatLong($address)
    {
        $address = str_replace(" ", "+", $address);
        $region = "Philippines";
        $apiKey = '';

        $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false&key=" . $apiKey);
        $json = json_decode($json);
      
        $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
        $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        return $lat.','.$long;
    }

    public function indexAction()
    {
        if ($this->getRequest()->isPost()) {
            $ids = $this->getRequest()->getPost('ids');
            if (count($ids) > 0) {
                foreach ($ids as $id) {
                    $this->getEventMapper()->delete($id);
                }
            } else {
                $this->flashMessenger()->setNamespace('error')->addMessage('Please select at least 1 event.');
                return $this->redirect()->toRoute('admin-event');
            }

            $this->flashMessenger()->setNamespace('success')->addMessage('Selected events successfully deleted.');
            return $this->redirect()->toRoute('admin-event');
        }

        $page = $this->params()->fromRoute('page');
        $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

        $filter = [];
        if (!empty($search_by)) {
            $filter = (array) json_decode($search_by);
        }
        $form = $this->getServiceLocator()->get('AdminEventSearchForm');
        $form->setData($filter);

        $order = ['created_datetime'];
        $paginator = $this->getEventMapper()->fetch(true, $filter, $order);
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        $paginator->setItemCountPerPage(10);

        return new ViewModel([
            'form' => $form,
            'paginator' => $paginator,
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
                return $this->redirect()->toRoute('admin-event', ['search_by' => $search_by]);
            } else {
                return $this->redirect()->toRoute('admin-event');
            }
        } else {
            return $this->redirect()->toRoute('admin-event');
        }
    }

    public function addAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $form = $this->getServiceLocator()->get('AdminEventAddForm');
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $form->setData($data);

            if ($form->isValid()) {
                $isError = false;

                if (!isset($_FILES['photo'])) {
                    $isError = true;
                    $form->get('photo')->setMessages(['Required field Company Logo.']);
                } else {
                    $allowed =  ['jpg'];
                    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    if (!in_array($ext, $allowed)) {
                        $isError = true;
                        $form->get('photo')->setMessages(["File type not allowed. Only " . implode(',', $allowed)]);
                    }
                    switch ($_FILES['photo']['error']) {
                      case 1:
                        $isError = true;
                        $form->get('photo')->setMessages(['The file is bigger than this PHP installation allows.']);
                        break;
                      case 2:
                        $isError = true;
                        $form->get('photo')->setMessages(['The file is bigger than this form allows.']);
                        break;
                      case 3:
                        $isError = true;
                        $form->get('photo')->setMessages(['Only part of the file was uploaded.']);
                        break;
                      case 4:
                        $isError = true;
                        $form->get('photo')->setMessages(['No file was uploaded.']);
                        break;
                      default:
                    }
                }

                $volunteerLimit = null;
                switch ($data['event_type']) {
                  case 'individual':
                    if (empty($data['volunteer_limit'])) {
                        $isError = true;
                        $form->get('teams')->setMessages(['Required Field Volunteer Limit.']);
                        break;
                    }

                    if (!is_numeric($data['volunteer_limit'])) {
                        $isError = true;
                        $form->get('teams')->setMessages(['Invalid Field Volunteer Limit.']);
                        break;
                    }

                    $volunteerLimit = $data['volunteer_limit'];
                    break;
                  case 'team':
                    if (!isset($data['teams']) || !is_array($data['teams']) || count($data['teams']) <= 0) {
                        $isError = true;
                        $form->get('teams')->setMessages(['Required Field Teams.']);
                        break;
                    }

                    foreach ($data['teams'] as $keyTeam => $team) {
                        $team = trim($team);
                        if ($team == '') {
                            $isError = true;
                            $form->get('teams')->setMessages(['Invalid Teams.']);
                            break;
                        }

                        if (!isset($data['team_limits'][$keyTeam]) || !is_numeric($data['team_limits'][$keyTeam])) {
                            $isError = true;
                            $form->get('tasks')->setMessages(['Invalid Team Limit.']);
                            break;
                        }
                    }
                    // no break
                  default:
                    break;
                }

                if (!isset($data['tasks']) || !is_array($data['tasks']) || count($data['tasks']) <= 0) {
                    $isError = true;
                    $form->get('tasks')->setMessages(['Required Field Tasks.']);
                }

                foreach ($data['tasks'] as $keyTask => $task) {
                    $task = trim($task);
                    if ($task == '') {
                        $isError = true;
                        $form->get('tasks')->setMessages(['Invalid Tasks.']);
                        break;
                    }

                    if (!isset($data['task_points'][$keyTask]) || !is_numeric($data['task_points'][$keyTask])) {
                        $isError = true;
                        $form->get('tasks')->setMessages(['Invalid Task Points.']);
                        break;
                    }
                }

                if (!$isError) {
                    $lat = $long = null;
                    list($lat, $long) = explode(",", $this->getLatLong($data['venue'] . " ". $data['city']));
                    
                    $event = new EventEntity;
                    $event->setName($data['name']);
                    $event->setCategoryId($data['category_id']);
                    $event->setDescription($data['description']);
                    $event->setVenue($data['venue']);
                    $event->setCity($data['city']);
                    $event->setLatitude($lat);
                    $event->setLongitude($long);
                    $event->setEventDate($data['event_date']);
                    $event->setEventType($data['event_type']);
                    $event->setVolunteerLimit($volunteerLimit);
                    $event->setOrganization($data['organization']);
                    $event->setContactName($data['contact_name']);
                    $event->setContactName($data['contact_name']);
                    $event->setContactEmail($data['contact_email']);
                    $event->setContactMobileNo($data['contact_mobile_no']);
                    $event->setWebsiteUrl($data['website_url']);
                    $event->setCreatedUserId($this->identity()->id);
                    $this->getEventMapper()->save($event);

                    switch ($data['event_type']) {
                      case 'individual':
                        break;
                      case 'team':
                        foreach ($data['teams'] as $teamKey => $team) {
                            $team = trim($team);

                            $eventTeam = new EventTeamEntity;
                            $eventTeam->setEventId($event->getId());
                            $eventTeam->setName($team);
                            $eventTeam->setTeamLimit($data['team_limits'][$teamKey]);
                            $eventTeam->setCreatedUserId($this->identity()->id);
                            $this->getEventTeamMapper()->save($eventTeam);
                        }
                        // no break
                      default:
                        break;
                    }

                    foreach ($data['tasks'] as $taskKey => $task) {
                        $task = trim($task);

                        $eventTask = new EventTaskEntity;
                        $eventTask->setEventId($event->getId());
                        $eventTask->setTask($task);
                        $eventTask->setPoints($data['task_points'][$taskKey]);
                        $eventTask->setCreatedUserId($this->identity()->id);
                        $this->getEventTaskMapper()->save($eventTask);
                    }

                    $directory = $config['pathEventPhoto']['absolutePath'] . $event->getId();
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755);
                    }

                    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    $destination = $directory . "/photo-750x450." . $ext;
                    if (!file_exists($destination)) {
                        move_uploaded_file($_FILES['photo']['tmp_name'], $destination);
                    }
                }

                $this->flashMessenger()->setNamespace('success')->addMessage('Event added successfully.');
                return $this->redirect()->toRoute('admin-event');
            } else {
                print_r($form->getMessages());

                $tempTasks = [];
                foreach ($data['tasks'] as $taskKey => $task) {
                    $tempTasks[] = [
                        'task' => $task,
                        'points' => $data['task_points'][$taskKey],
                    ];
                }
                $tasks = $tempTasks;

                $teams = [];
                switch ($data['event_type']) {
                  case 'individual':
                    break;
                  case 'team':
                    $tempTeams = [];
                    foreach ($data['teams'] as $teamKey => $team) {
                        $tempTeams[] = [
                            'name' => $team,
                            'team_limit' => $data['team_limits'][$teamKey],
                        ];
                    }
                    $teams = $tempTeams;
                    break;
                  default:
                    break;
                }
            }
        } else {
            $tasks = [];
            $teams = [];
        }

        return new ViewModel([
            'form' => $form,
            'config' => $config,
            'teams' => $teams,
            'tasks' => $tasks,
        ]);
    }

    public function editAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid event.');
            return $this->redirect()->toRoute('admin-event');
        }
        $event = $this->getEventMapper()->getEvent($id);
        if (!$event) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
            return $this->redirect()->toRoute('admin-event');
        }

        $config = $this->getServiceLocator()->get('Config');

        $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');

        $form = new AdminEventEditForm($dbAdapter, $this->getCategoryMapper());
        $form->bind($event);
        $form->get('submit')->setAttribute('value', 'Edit');
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($this->getRequest()->getPost()->toArray());
            if ($form->isValid()) {
                $lat = $long = null;
                list($lat, $long) = explode(",", $this->getLatLong($data['venue'] . " ". $data['city']));

                $event->setName($data['name']);
                $event->setCategoryId($data['category_id']);
                $event->setDescription($data['description']);
                $event->setVenue($data['venue']);
                $event->setCity($data['city']);
                $event->setLatitude($lat);
                $event->setLongitude($long);
                $event->setEventDate($data['event_date']);
                $event->setEventType($data['event_type']);
                $event->setVolunteerLimit($volunteerLimit);
                $event->setOrganization($data['organization']);
                $event->setContactName($data['contact_name']);
                $event->setContactName($data['contact_name']);
                $event->setContactEmail($data['contact_email']);
                $event->setContactMobileNo($data['contact_mobile_no']);
                $event->setWebsiteUrl($data['website_url']);
                $event->setModifiedDatetime(date('Y-m-d H:i:s'));
                $event->setModifiedUserId($this->identity()->id);
                $this->getEventMapper()->save($event);

                switch ($data['event_type']) {
                      case 'individual':
                        break;
                      case 'team':
                        foreach ($data['teams'] as $teamKey => $team) {
                            $team = trim($team);

                            $eventTeam = new EventTeamEntity;
                            $eventTeam->setEventId($event->getId());
                            $eventTeam->setName($team);
                            $eventTeam->setTeamLimit($data['team_limits'][$teamKey]);
                            $eventTeam->setCreatedUserId($this->identity()->id);
                            $this->getEventTeamMapper()->save($eventTeam);
                        }
                        // no break
                      default:
                        break;
                    }

                foreach ($data['tasks'] as $taskKey => $task) {
                    $task = trim($task);

                    $eventTask = new EventTaskEntity;
                    $eventTask->setEventId($event->getId());
                    $eventTask->setTask($task);
                    $eventTask->setPoints($data['task_points'][$taskKey]);
                    $eventTask->setCreatedUserId($this->identity()->id);
                    $this->getEventTaskMapper()->save($eventTask);
                }

                $isError = false;

                if (isset($_FILES['photo']) && !empty($_FILES['photo']['name'])) {
                    $allowed =  ['jpg'];
                    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    if (!in_array($ext, $allowed)) {
                        $isError = true;
                        $form->get('photo')->setMessages(["File type not allowed. Only " . implode(',', $allowed)]);
                    }
                    switch ($_FILES['photo']['error']) {
                        case 1:
                          $isError = true;
                          $form->get('photo')->setMessages(['The file is bigger than this PHP installation allows.']);
                          break;
                        case 2:
                          $isError = true;
                          $form->get('photo')->setMessages(['The file is bigger than this form allows.']);
                          break;
                        case 3:
                          $isError = true;
                          $form->get('photo')->setMessages(['Only part of the file was uploaded.']);
                          break;
                        case 4:
                          $isError = true;
                          $form->get('photo')->setMessages(['No file was uploaded.']);
                          break;
                        default:
                      }

                    $directory = $config['pathEventPhoto']['absolutePath'] . $event->getId();
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755);
                    }

                    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    $destination = $directory . "/photo-orig." . $ext;
                    if (!file_exists($destination)) {
                        move_uploaded_file($_FILES['photo']['tmp_name'], $destination);
                    }
                    $destination2 = $directory . "/photo-crop-750x450." . $ext;
                    if (file_exists($destination2)) {
                        unlink($destination2);
                    }
                    $image = new ImageResize($destination);
                    $image->crop(750, 450);
                    $image->save($destination2);
                    if (file_exists($destination)) {
                        unlink($destination);
                    }

                    $destination3 = $directory . "/photo-750x450." . $ext;
                    if (file_exists($destination3)) {
                        unlink($destination3);
                    }
                    $image = new ImageResize($destination2);
                    $image->resize(750, 450, $allow_enlarge = true);
                    $image->save($destination3);
                    if (file_exists($destination2)) {
                        unlink($destination2);
                    }
                }

                $this->flashMessenger()->setNamespace('success')->addMessage('Event edited successfully.');
                return $this->redirect()->toRoute('admin-event');
            }

            // print_r($form->getMessages());

            $tempTasks = [];
            foreach ($data['tasks'] as $taskKey => $task) {
                $tempTasks[] = [
                    'task' => $task,
                    'points' => $data['task_points'][$taskKey],
                ];
            }
            $tasks = $tempTasks;

            $teams = [];
            switch ($data['event_type']) {
              case 'individual':
                break;
              case 'team':
                $tempTeams = [];
                foreach ($data['teams'] as $teamKey => $team) {
                    $tempTeams[] = [
                        'name' => $team,
                        'team_limit' => $data['team_limits'][$teamKey],
                    ];
                }
                $teams = $tempTeams;
                break;
              default:
                break;
            }
        } else {
            $tempTasks = [];
            $filter = [
                'event_id' => $event->getId(),
            ];
            $tasks = $this->getEventTaskMapper()->fetch(false, $filter);
            if (count($tasks) > 0) {
                foreach ($tasks as $task) {
                    $tempTasks[] = [
                        'task' => $task->getTask(),
                        'points' => $task->getPoints(),
                    ];
                }
            }
            $tasks = $tempTasks;

            $tempTeams = [];
            $teams = [];
            switch ($event->getEventType()) {
              case 'individual':
                break;
              case 'team':
                  $filter = [
                      'event_id' => $event->getId(),
                  ];
                $teams = $this->getEventTeamMapper()->fetch(false, $filter);
                if (count($teams) > 0) {
                    foreach ($teams as $team) {
                        $tempTeams[] = [
                            'name' => $team->getName(),
                            'team_limit' => $team->getTeamLimit(),
                        ];
                    }
                }
                $teams = $tempTeams;
                break;
              default:
                break;
            }
        }

        return new ViewModel([
            'form' => $form,
            'config' => $config,
            'event' => $event,
            'teams' => $teams,
            'tasks' => $tasks,
        ]);
    }

    public function ajaxGetCoordinatesAction()
    {
        header('Content-Type: application/json;');

        $results = [
            'success' => false,
        ];

        $request = $this->getRequest();
        $response = $this->getResponse();

        if ($request->isPost()) {
            $venue = trim($request->getPost('venue'));
            $city = trim($request->getPost('city'));

            if ($venue!='' && $city!='') {
                $lat = $long = null;
                list($lat, $long) = explode(",", $this->getLatLong($venue . " ". $city));

                $results = [
                    'success' => true,
                    'lat' => $lat,
                    'long' => $long,
                ];
            }
        }
 
        $json = json_encode($results);

        $this->response->setContent($json);

        return $this->response;
    }

    public function deleteAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid event.');
            return $this->redirect()->toRoute('admin-event');
        }
        $event = $this->getEventMapper()->getEvent($id);
        if (!$event) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
            return $this->redirect()->toRoute('admin-event');
        }

        $config = $this->getServiceLocator()->get('Config');
        $directory = $config['pathEventPhoto']['absolutePath'] . $event->getId();

        $files = glob($directory . "/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        if (!file_exists($directory)) {
            rmdir($directory);
        }

        $this->getEventMapper()->delete($id);

        $this->flashMessenger()->setNamespace('success')->addMessage('Event deleted successfully.');
        return $this->redirect()->toRoute('admin-event');
    }

    public function attendanceAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid event.');
            return $this->redirect()->toRoute('admin-event');
        }
        $event = $this->getEventMapper()->getEvent($id);
        if (!$event) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
            return $this->redirect()->toRoute('admin-event');
        }

        $route = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
        $action = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getParam('action');

        return new ViewModel([
            'route' => $route,
            'action' => $action,
            'event' => $event,
        ]);
    }

    public function checkinAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            if (!isset($data['event_id'])) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
                return $this->redirect()->toRoute('admin-event');
            }
            
            $event = $this->getEventMapper()->getEvent($data['event_id']);
            if (!$event) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
                return $this->redirect()->toRoute('admin-event');
            }
          
            $authService = $this->serviceLocator->get('auth_service');
            if (!$authService->getIdentity()) {
                $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
                return $this->redirect()->toRoute('login');
            }
            $user_id = $authService->getIdentity()->id;

            if (!isset($data['email'])) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Required Field: Eamil.');
                return $this->redirect()->toRoute('admin-event', ['action' => 'attendance', 'id' => $event->getId()]);
            }

            $user = $this->getUserMapper()->getUserByEmail($data['email']);
            if (!$user) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Eamil.');
                return $this->redirect()->toRoute('admin-event', ['action' => 'attendance', 'id' => $event->getId()]);
            }

            $userEvent = $this->getUserEventMapper()->getUserEventByUserIdAndEventId($user->getId(), $event->getId());
            if (!$userEvent) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Eamil is not a volunteer of this event.');
                return $this->redirect()->toRoute('admin-event', ['action' => 'attendance', 'id' => $event->getId()]);
            }

            $userEvent->setAttendDatetime(date('Y-m-d H:i:s'));
            $this->getUserEventMapper()->save($userEvent);

            $this->flashMessenger()->setNamespace('success')->addMessage('You succesfully check in in this event.');
            return $this->redirect()->toRoute('admin-event', ['action' => 'attendance-view', 'id' => $event->getId(), 'userid' => $user->getId()]);
        } else {
            return $this->redirect()->toRoute('admin-event');
        }
    }

    public function attendanceViewAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid event.');
            return $this->redirect()->toRoute('admin-event');
        }
        $event = $this->getEventMapper()->getEvent($id);
        if (!$event) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
            return $this->redirect()->toRoute('admin-event');
        }

        $userId = (int)$this->params('userid');
        if (!$userId) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Profile.');
            return $this->redirect()->toRoute('admin-event');
        }
        $user = $this->getUserMapper()->getUser($userId);
        if (!$user) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Profile.');
            return $this->redirect()->toRoute('admin-event', ['action' => 'attendance', 'id' => $event->getId()]);
        }

        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

        $searchFilter = [];
        if (!empty($search_by)) {
            $searchFilter = (array) json_decode($search_by);
        }
        $searchFilter['user_id'] = $user->getId();

        $order = ['created_datetime DESC'];
        $paginator = $this->getUserEventMapper()->getUserEvents(true, $searchFilter, $order);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(12);

        return new ViewModel([
            'user' => $user,
            'event' => $event,
            'paginator' => $paginator,
            'search_by' => $search_by,
            'page' => $page,
            'searchFilter' => $searchFilter,
        ]);
    }

    public function tasksAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid event.');
            return $this->redirect()->toRoute('admin-event');
        }
        $event = $this->getEventMapper()->getEvent($id);
        if (!$event) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
            return $this->redirect()->toRoute('admin-event');
        }

        $filter = [
            'event_id' => $event->getId(),
        ];
        $order = [
            'id',
        ];
        $tasks = $this->getEventTaskMapper()->fetch(false, $filter, $order);

        $route = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
        $action = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getParam('action');

        return new ViewModel([
            'route' => $route,
            'action' => $action,
            'event' => $event,
            'tasks' => $tasks,
        ]);
    }

    public function taskPointsAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            if (!isset($data['event_id'])) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
                return $this->redirect()->toRoute('admin-event');
            }
            
            $event = $this->getEventMapper()->getEvent($data['event_id']);
            if (!$event) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
                return $this->redirect()->toRoute('admin-event');
            }

            if (!isset($data['task_id'])) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Task.');
                return $this->redirect()->toRoute('admin-event', ['action' => 'tasks', 'id' => $event->getId()]);
            }

            $eventTask = $this->getEventTaskMapper()->getEventTask($data['task_id']);
            if (!$eventTask) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Task.');
                return $this->redirect()->toRoute('admin-event', ['action' => 'tasks', 'id' => $event->getId()]);
            }

            if ($eventTask->getEventId() != $event->getId()) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Task.');
                return $this->redirect()->toRoute('admin-event', ['action' => 'tasks', 'id' => $event->getId()]);
            }

            $authService = $this->serviceLocator->get('auth_service');
            if (!$authService->getIdentity()) {
                $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
                return $this->redirect()->toRoute('login');
            }
            $user_id = $authService->getIdentity()->id;

            if (!isset($data['email'])) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Required Field: Eamil.');
                return $this->redirect()->toRoute('admin-event', ['action' => 'tasks', 'id' => $event->getId()]);
            }

            $user = $this->getUserMapper()->getUserByEmail($data['email']);
            if (!$user) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Eamil.');
                return $this->redirect()->toRoute('admin-event', ['action' => 'tasks', 'id' => $event->getId()]);
            }

            $userEvent = $this->getUserEventMapper()->getUserEventByUserIdAndEventId($user->getId(), $event->getId());
            if (!$userEvent) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Eamil is not a volunteer of this event.');
                return $this->redirect()->toRoute('admin-event', ['action' => 'tasks', 'id' => $event->getId()]);
            }

            if (empty($userEvent->getAttendDatetime())) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Volunteer is not check in on this event.');
                return $this->redirect()->toRoute('admin-event', ['action' => 'tasks', 'id' => $event->getId()]);
            }

            $filter = [
                'user_id' => $user->getId(),
                'event_task_id' => $eventTask->getId(),
            ];
            $userEventTask = $this->getUserEventTaskMapper()->fetch(false, $filter);
            if (count($userEventTask) > 0) {
                // $this->flashMessenger()->setNamespace('error')->addMessage('You already earn points in this event task.');
                // return $this->redirect()->toRoute('admin-event', ['action' => 'tasks', 'id' => $event->getId()]);
            } else {
                $userEventTask = new UserEventTaskEntity;
                $userEventTask->setEventTaskId($eventTask->getId());
                $userEventTask->setUserId($user->getId());
                $userEventTask->setCreatedUserId($user_id);
                $this->getUserEventTaskMapper()->save($userEventTask);
            }

            $this->flashMessenger()->setNamespace('success')->addMessage('You succesfully earn points in this event task.');
            return $this->redirect()->toRoute('admin-event', ['action' => 'task-view', 'id' => $event->getId(), 'userid' => $user->getId()]);
        } else {
            return $this->redirect()->toRoute('admin-event');
        }
    }

    public function taskViewAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid event.');
            return $this->redirect()->toRoute('admin-event');
        }
        $event = $this->getEventMapper()->getEvent($id);
        if (!$event) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
            return $this->redirect()->toRoute('admin-event');
        }

        $userId = (int)$this->params('userid');
        if (!$userId) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Profile.');
            return $this->redirect()->toRoute('admin-event');
        }
        $user = $this->getUserMapper()->getUser($userId);
        if (!$user) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Profile.');
            return $this->redirect()->toRoute('admin-event', ['action' => 'attendance', 'id' => $event->getId()]);
        }

        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

        $searchFilter = [];
        if (!empty($search_by)) {
            $searchFilter = (array) json_decode($search_by);
        }
        $searchFilter['user_id'] = $user->getId();

        $order = ['created_datetime DESC'];
        $paginator = $this->getUserEventMapper()->getUserEvents(true, $searchFilter, $order);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(12);

        return new ViewModel([
            'user' => $user,
            'event' => $event,
            'paginator' => $paginator,
            'search_by' => $search_by,
            'page' => $page,
            'searchFilter' => $searchFilter,
        ]);
    }

    public function volunteersAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid event.');
            return $this->redirect()->toRoute('admin-event');
        }
        $event = $this->getEventMapper()->getEvent($id);
        if (!$event) {
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Event.');
            return $this->redirect()->toRoute('admin-event');
        }

        $route = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
        $action = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getParam('action');

        $page = $this->params()->fromRoute('page');
        $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

        $filter = [
            'event_id' => $event->getId(),
        ];

        $order = ['first_name', 'last_name'];
        $paginator = $this->getUserEventMapper()->getUserEvents(false, $filter, $order);
      
        return new ViewModel([
            'route' => $route,
            'action' => $action,
            'paginator' => $paginator,
        ]);
    }
}
