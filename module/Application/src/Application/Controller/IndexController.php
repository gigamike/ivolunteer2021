<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use GeoIp2\Database\Reader;

class IndexController extends AbstractActionController
{
    public function getEventMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('EventMapper');
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

        // get latest event
        $filter = [];
        $order = ['event_date DESC'];
        $limit = 1;
        $latestEvent = $this->getEventMapper()->fetch(false, $filter, $order, $limit);

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

        $order = ['distance', 'event_date DESC'];
        $paginator = $this->getEventMapper()->getEvents(true, $searchFilter, $order);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(12);

        return new ViewModel([
            'latestEvent' => $latestEvent,
            'paginator' => $paginator,
            'search_by' => $search_by,
            'page' => $page,
            'searchFilter' => $searchFilter,
            'route' => $route,
        ]);
    }
}
