<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Event\Controller\Index' => 'Event\Controller\IndexController',
						'Event\Controller\Admin' => 'Event\Controller\AdminController',
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'event' => __DIR__ . '/../view',
				),
		),
);
