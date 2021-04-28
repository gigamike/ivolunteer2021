<?php

namespace Volunteer\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message as Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Sendmail as Sendmail;

use User\Model\UserEventEntity;

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

    public function indexAction()
    {
        $route = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';

        $searchFilter = [
            'role' => 'member',
        ];
        if (!empty($search_by)) {
            $searchFilter = (array) json_decode($search_by);
        }

        $order = ['points DESC'];
        $paginator = $this->getUserMapper()->getTopVolunteers(true, $searchFilter, $order);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(12);

        return new ViewModel([
            'paginator' => $paginator,
            'search_by' => $search_by,
            'page' => $page,
            'searchFilter' => $searchFilter,
            'route' => $route,
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
}
