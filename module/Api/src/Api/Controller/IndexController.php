<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

use Cart\Model\CartEntity;
use Incentive\Model\IncentiveEntity;

class IndexController extends AbstractActionController
{
	public function getEventMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('EventMapper');
  }

	/*
	* https://apitester.com/
	*
	*/
	public function indexAction()
	{
		$config = $this->getServiceLocator()->get('Config');

		return new ViewModel(array(
			'config' => $config,
    ));
	}

	private function _getResponseWithHeader()
  {
      $response = $this->getResponse();
      $response->getHeaders()
               // make can accessed by *
               ->addHeaderLine('Access-Control-Allow-Origin','*')
               // set allow methods
               ->addHeaderLine('Access-Control-Allow-Methods','POST PUT DELETE GET')
							 // json
							 ->addHeaderLine('Content-Type', 'application/json');
      return $response;
  }

	/*
	* https://clashofcodes2019.gigamike.net/api/events
	*
	*
	*/
	public function eventsAction()
	{
		$isDebug = true;

		$results = array();

		$reply = "";

		$filter = array(
			'date_greater_than' => date('Y-m-d', strtotime('yesterday')),
		);
		$order = array('event_date DESC');
		$limit = 3;
		$events = $this->getEventMapper()->fetch(false, $filter, $order);
		if(count($events) > 0){
			$reply = "Incoming events are as follows. ";

			$ctr = 0;
			foreach ($events as $event) {
				$reply .= date('M d, Y', strtotime($event->getEventDate()))  . " " . $event->getName() . " at " . $event->getVenue() . ". ";
			}
		}else{
			$reply = "Im sorry no incoming events!";
		}

		$results['text'] = $reply;

		$response = $this->_getResponseWithHeader()->setContent(json_encode($results));
    return $response;
	}
}
