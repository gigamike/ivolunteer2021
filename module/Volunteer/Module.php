<?php
namespace Volunteer;

use Event\Model\EventMapper;
use Event\Model\EventTaskMapper;
use Event\Model\EventTeamMapper;
use Event\Form\AdminEventSearchForm;
use Event\Form\AdminEventAddForm;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'EventMapper' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper = new EventMapper($dbAdapter);
                    return $mapper;
                },
                'EventTaskMapper' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper = new EventTaskMapper($dbAdapter);
                    return $mapper;
                },
                'EventTeamMapper' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper = new EventTeamMapper($dbAdapter);
                    return $mapper;
                },
            ],
        ];
    }
}
