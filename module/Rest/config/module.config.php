<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Rest;

return array(
    'controllers' => array(
        'invokables' => array(
            'Rest\Controller\Index'         => Controller\IndexController::class,
            'Rest\Controller\SocialMedia'   => Controller\SocialMediaController::class,
            'Rest\Controller\Users'         => Controller\UsersController::class,
            'Rest\Controller\Friends'        => Controller\FriendsController::class,
            'Rest\Controller\Status'        => Controller\StatusController::class,
            'Rest\Restful\RestSocialMedia'	=> Restful\RestSocialMediaController::class,
            'Rest\Restful\RestUsers'        => Restful\RestUsersController::class,
            'Rest\Restful\RestFriends'      => Restful\RestFriendsController::class,
            'Rest\Restful\RestStatus'      => Restful\RestStatusController::class,
        ),
    ),
    'module_layouts' => array(
        'Rest' => 'layout/rest',
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
            'Zend\Db\Adapter\AdapterAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'         => __DIR__ . '/../view/layout/rest.phtml',
            'rest/index/index'      => __DIR__ . '/../view/rest/index/index.phtml',
            'error/404'             => __DIR__ . '/../view/error/404.phtml',
            'error/index'           => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'php_renderer' => array(
            'parameters' => array(
                'resolver' => 'Zend\View\Resolver\AggregateResolver',
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Rest\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'rest' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/rest',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Rest\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'rest-sm' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/rest-sm',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Rest\Restful',
                        'controller'    => 'RestSocialMedia',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'sm' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            //'route'    => '[/:method]',
                            'route'    => '/:method',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Rest\Controller',
                                'controller' => 'SocialMedia',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                ),
            ),
            'rest-usr' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/rest-usr',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Rest\Restful',
                        'controller'    => 'RestUsers',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'usr' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:method',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Rest\Controller',
                                'controller' => 'Users',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                ),
            ),
            'rest-frnd' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/rest-frnd',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Rest\Restful',
                        'controller'    => 'RestFriends',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'frnd' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:method',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Rest\Controller',
                                'controller' => 'Friends',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                ),
            ),
            'rest-stts' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/rest-stts',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Rest\Restful',
                        'controller'    => 'RestStatus',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'stts' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:method',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Rest\Controller',
                                'controller' => 'Status',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
