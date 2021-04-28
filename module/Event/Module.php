<?php
namespace Event;

use Event\Model\EventMapper;
use Event\Model\EventTaskMapper;
use Event\Model\EventTeamMapper;
use Event\Model\EventTeamMemberMapper;
use Event\Form\EventSearchForm;
use Event\Form\EventAddForm;
use Event\Form\AdminEventSearchForm;
use Event\Form\AdminEventAddForm;
use Event\Form\AdminEventEditForm;
use Category\Model\CategoryMapper;

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
                'EventTeamMemberMapper' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper = new EventTeamMemberMapper($dbAdapter);
                    return $mapper;
                },
                'EventSearchForm' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $categoryMapper = new CategoryMapper($dbAdapter);
                     
                    $form = new EventSearchForm($dbAdapter, $categoryMapper);
                    return $form;
                },

                'AdminEventSearchForm' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $form = new AdminEventSearchForm($dbAdapter);
                    return $form;
                },
                'AdminEventAddForm' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $categoryMapper = new CategoryMapper($dbAdapter);

                    $form = new AdminEventAddForm($dbAdapter, $categoryMapper);
                    return $form;
                },
                'AdminEventEditForm' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $categoryMapper = new CategoryMapper($dbAdapter);

                    $form = new AdminEventEditForm($dbAdapter, $categoryMapper);
                    return $form;
                },
            ],
        ];
    }
}
