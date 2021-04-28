<?php

namespace Member\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Gumlet\ImageResize;

use Dompdf\Dompdf;
use Dompdf\Options;

class IndexController extends AbstractActionController
{
    public function getUserMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('UserMapper');
    }

    public function getUserEventMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('UserEventMapper');
    }

    public function getEventMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('EventMapper');
    }

    public function indexAction()
    {
        $route = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
        $action = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getParam('action');

        $page = $this->params()->fromRoute('page');
        $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

        $filter = [];
        if (!empty($search_by)) {
            $filter = (array) json_decode($search_by);
        }

        $user = $this->getUserMapper()->getUser($this->identity()->id);
        if (!$user) {
            $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
            return $this->redirect()->toRoute('login');
        }

        $config = $this->getServiceLocator()->get('Config');

        $filter = [];
        if (!empty($search_by)) {
            $filter = (array) json_decode($search_by);
        }
        $filter = [
            'user_id' => $user->getId(),
        ];

        $order = ['created_datetime DESC'];
        $paginator = $this->getUserEventMapper()->getUserEvents(true, $filter, $order);
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        $paginator->setItemCountPerPage(10);

        return new ViewModel([
            'route' => $route,
            'action' => $action,
            'user' => $user,
            'config' => $config,
            'paginator' => $paginator,
        ]);
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

        $config = $this->getServiceLocator()->get('Config');

        $html = "";
        $html .= '<div style="width:800px; height:600px; padding:20px; text-align:center; border: 10px solid #787878">
<div style="width:750px; height:550px; padding:20px; text-align:center; border: 5px solid #787878">
        <img src="https://www.gigamike.net/img/ivolunteerLogo.png" style="height:80px"><br>
       <span style="font-size:50px; font-weight:bold">Certificate of Attendance</span>
       <br><br>
       <span style="font-size:25px"><i>This is to certify that</i></span>
       <br><br>
       <span style="font-size:30px"><b>' . $user->getFirstName() . ' ' . $user->getLastName() . '</b></span><br/><br/>
       <span style="font-size:25px"><i>has attended</i></span> <br/><br/>
       <span style="font-size:30px">' . $event->getName() . '</span> <br/><br/>
       <span style="font-size:20px">' . date('M d, Y', strtotime($event->getCreatedDatetime())) . ' at ' . $event->getVenue() . '</span> <br/><br/><br/>
       <img src="https://www.gigamike.net/img/digitalsignature.jpg" style="height:80px"><br>
      <span style="font-size:20px"><b>JB Tan</b></span><br>
      <span style="font-size:15px">Founder iVolunteer PH</span>
</div>
</div>';
        

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

    public function profileAction()
    {
        $route = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
        $action = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getParam('action');

        $user = $this->getUserMapper()->getUser($this->identity()->id);
        if (!$user) {
            $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel([
            'route' => $route,
            'action' => $action,
            'user' => $user,
        ]);
    }

    public function referralsAction()
    {
        $route = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
        $action = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getParam('action');

        $user = $this->getUserMapper()->getUser($this->identity()->id);
        if (!$user) {
            $this->flashMessenger()->setNamespace('error')->addMessage('You need to login or register first.');
            return $this->redirect()->toRoute('login');
        }

        $config = $this->getServiceLocator()->get('Config');

        $page = $this->params()->fromRoute('page');
        $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

        $filter = [];
        if (!empty($search_by)) {
            $filter = (array) json_decode($search_by);
        }
        $filter = [
            'referral_id' => $user->getId(),
        ];

        $order = ['first_name', 'last_name', 'email'];
        $paginator = $this->getUserMapper()->fetch(true, $filter, $order);
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        $paginator->setItemCountPerPage(10);

        return new ViewModel([
            'route' => $route,
            'action' => $action,
            'user' => $user,
            'config' => $config,
            'paginator' => $paginator,
        ]);
    }
}
