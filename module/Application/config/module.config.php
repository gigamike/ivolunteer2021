<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'terms-of-use' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/terms-of-use',
                    'defaults' => [
                        'controller' => 'Page\Controller\Index',
                        'action'     => 'termsOfUse',
                    ],
                ],
            ],
            'privacy-policy' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/privacy-policy',
                    'defaults' => [
                        'controller' => 'Page\Controller\Index',
                        'action'     => 'privacyPolicy',
                    ],
                ],
            ],
            'about' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/about',
                    'defaults' => [
                        'controller' => 'Page\Controller\Index',
                        'action'     => 'about',
                    ],
                ],
            ],
            'contact' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/contact',
                    'defaults' => [
                        'controller' => 'Contact\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'facebook' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/facebook[/:action][/:id][/page/:page][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'Facebook\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'google' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/google[/:action][/:id][/page/:page][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'Google\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'service' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/service',
                    'defaults' => [
                        'controller' => 'Page\Controller\Index',
                        'action'     => 'service',
                    ],
                ],
            ],
            'contact' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/contact',
                    'defaults' => [
                        'controller' => 'Contact\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'registration' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/registration[/:action][/:id][/page/:page][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'User\Controller\Registration',
                        'action'     => 'index',
                    ],
                ],
            ],
            'login' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/login[/:action][/:id][/page/:page][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'User\Controller\Auth',
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => 'User\Controller\Auth',
                        'action'     => 'logout',
                    ],
                ],
            ],
            'forgot-password' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/forgot-password',
                    'defaults' => [
                        'controller' => 'User\Controller\ForgotPassword',
                        'action'     => 'index',
                    ],
                ],
            ],
            'admin' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/admin[/:action][/:id][/page/:page][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'chatbot' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/chatbot[/:action][/:id][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'Chatbot\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'member' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/member[/:action][/:id][/status/:status][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'Member\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'api' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/api[/:action][/:id][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'Api\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'admin-user' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/admin/user[/:action][/:id][/page/:page][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'User\Controller\Admin',
                        'action'     => 'index',
                    ],
                ],
            ],
            'admin-event' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/admin/event[/:action][/:id][/:userid][/page/:page][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'userid' => '[0-9]+',
                        'page' => '[0-9]+',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'Event\Controller\Admin',
                        'action'     => 'index',
                    ],
                ],
            ],
            'events' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/events[/:action][/:id][/page/:page][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'Event\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'volunteers' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/volunteers[/:action][/:id][/page/:page][/search_by/:search_by]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                        'search_by' => '.*',
                    ],
                    'defaults' => [
                        'controller' => 'Volunteer\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/application',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [
                'cron-test' => [
                    'options' => [
                        'route'    => 'cron-test',
                        'defaults' => [
                            'controller' => 'Cron\Controller\Index',
                            'action'     => 'index'
                        ],
                    ],
                ],
            ],
        ],
    ],
];
