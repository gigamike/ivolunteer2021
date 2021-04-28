<?php
return [
    'controllers' => [
        'invokables' => [
            'Volunteer\Controller\Index' => 'Volunteer\Controller\IndexController',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'volunteer' => __DIR__ . '/../view',
        ],
    ],
];
