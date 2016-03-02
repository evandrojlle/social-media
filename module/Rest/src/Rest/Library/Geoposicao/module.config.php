<?php
$pathSaveSession = (isset($_SERVER['WINDIR'])) ? 'C:/tmp/' : '/home/c7web/public_html/catarq/public/tmp/';

return array(
    'controllers' => array(
        'invokables' => array(
        	'auth' 					=> 'Admin\Controller\AuthController',
            'index' 				=> 'Admin\Controller\IndexController',
            'planos'				=> 'Admin\Controller\PlanosController',
            'empresas'				=> 'Admin\Controller\EmpresasController',
            'assinaturas'			=> 'Admin\Controller\AssinaturasController',
            'tp-pessoa'				=> 'Admin\Controller\TipoPessoaController',
            'segmentos'				=> 'Admin\Controller\SegmentosController',
            'paginas'				=> 'Admin\Controller\PaginasController',
            'tp-paginas'			=> 'Admin\Controller\TipoPaginaController',
            'geo-posicionamento'	=> 'Admin\Controller\GeoPosicionamentoController',
        ),
    ),
	'router' => array(
        'routes' => array(
        	'admin' => array(
        		'type'    => 'Literal',
        		'options' => array(
        			'route'    => '/admin',
        			'defaults' => array(
        				'controller'    => 'index',
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
        					'defaults' => array(),
        				),
        			),
        		),
        	),
        	'login-adm' => array(
        		'type' => 'Literal',
        		'options' => array(
        			'route'    => '/auth/login',
        			'defaults' => array(
        				'__NAMESPACE__'	=> 'Admin\Controller',
        				'controller' 	=> 'auth',
        				'action'     	=> 'login',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'process' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '/[:action]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        		),
        	),
        	'dashboard' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/dashboard[/]',
                    'defaults' => array(
                        'controller' => 'index',
                        'action'     => 'index',
                    ),
                ),
            ),
        	'planos' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/planos[/]',
                    'defaults' => array(
                        'controller' => 'planos',
                        'action'     => 'index',
                    ),
                ),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'edit' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        			'del' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        		),
            ),
        		
        	'tp-planos' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route'    => '/tp-planos[/]',
        			'defaults' => array(
        				'controller' => 'planos',
        				'action'     => 'tp-planos',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'add' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        			'edit-tp-plano' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(
        						'controller' => 'planos',
        						'action'     => 'edit-tp-plano',
        					),
        				),
        			),
        			'delete-tp-plano' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        		),
        	),
        			
        	'add-plano' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/planos/add[/]',
                    'defaults' => array(
                        'controller' => 'planos',
                        'action'     => 'add',
                    ),
                ),
            ),
        	'add-tp-plano' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/planos/add-tp-plano[/]',
                    'defaults' => array(
                        'controller' => 'planos',
                        'action'     => 'add-tp-plano',
                    ),
                ),
            ),
        	'subscribers' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/subscribers[/]',
                    'defaults' => array(
                        'controller' => 'assinaturas',
                        'action'     => 'index',
                    ),
                ),
            ),
        	'payments' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/admin/payments[/]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Pagamentos',
                        'action'     => 'index',
                    ),
                ),
            ),
        	
        	'empresas' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route'    => '/empresas[/]',
        			'defaults' => array(
        				'controller' => 'empresas',
        				'action'     => 'index',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'add' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => 'add[/]',
        					'defaults' => array(
        						'controller' => 'empresas',
        						'action'     => 'add',
        					),
        				),
        			),
        			'edit' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        			'delete' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        			'carrega-cep-ajax' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'defaults' => array(
        						'controller' => 'empresas',
        						'action'     => 'carrega-cep-ajax',
        					),
        				),
        			),
        		),
        	),
        		
        	'tp-pessoa' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route'    => '/tp-pessoa[/]',
        			'defaults' => array(
        				'controller' => 'tp-pessoa',
        				'action'     => 'index',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'add' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        			'edit' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        			'delete' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        		),
        	),
        	
        	'segmentos' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route'    => '/segmentos[/]',
        			'defaults' => array(
        				'controller' => 'segmentos',
        				'action'     => 'index',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'add' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        			'edit' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        			'delete' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        		),
        	),
        		
        	'paginas' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route'    => '/paginas[/page/:page]',
        			'defaults' => array(
        				'controller' => 'paginas',
        				'action'     => 'index',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'paginator' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route'    => '[page/:page]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'page' => 'd+'
        					),
        					'defaults' => array(
        						'controller' => 'paginas',
        						'action' => 'index',
        						'page' => 1
        					),
        				),
        			),
        		),
        	),
        		
        	'add-pagina' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route'    => '/paginas/add[/]',
        			'defaults' => array(
        				'controller' => 'paginas',
        				'action'     => 'add',
        			),
        		),
        	),
        	
        	'edit-pagina' => array(
        		'type'    => 'Segment',
        		'options' => array(
        			'route'    => '/paginas/edit[/:id]',
        			'defaults' => array(
        				'controller' => 'paginas',
        				'action'     => 'edit',
        			),
        		),
        	),
        		
        	'delete-pagina' => array(
        		'type'    => 'Segment',
        		'options' => array(
        			'route'    => '/paginas/delete[/:id]',
        			'defaults' => array(
        				'controller' => 'paginas',
        				'action'     => 'delete',
        			),
        		),
        	),
        	
        	'tp-paginas' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route'    => '/tp-paginas[/]',
        			'defaults' => array(
        				'controller' => 'tp-paginas',
        				'action'     => 'index',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'add' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        			'edit' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        			'delete' => array(
        				'type'    => 'Segment',
        				'options' => array(
        					'route'    => '[:action][/:id]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(),
        				),
        			),
        		),
        	),
        	
        	'geo-pos' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route'    => '/geo-pos[/page/:page]',
        			'defaults' => array(
        				'controller' => 'geo-posicionamento',
        				'action'     => 'index',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'paginator' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route'    => '[page/:page]',
        					'constraints' => array(
        						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'page' => 'd+'
        					),
        					'defaults' => array(
        						'controller' => 'geo-posicionamento',
        						'action' => 'index',
        						'page' => 1
        					),
        				),
        			),
        		),
        	),
        	
        	'add-geo-pos' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route'    => '/geo-pos/add[/]',
        			'defaults' => array(
        				'controller' => 'geo-posicionamento',
        				'action'     => 'add',
        			),
        		),
        	),
        		
        	'carrega-geo-pos-ajax' => array(
        		'type'    => 'Segment',
        		'options' => array(
        			'route'    => '/geo-pos/carrega-geo-pos-ajax[/:id]',
        			'defaults' => array(
        				'controller' => 'geo-posicionamento',
        				'action'     => 'carrega-geo-pos-ajax',
        			),
        		),
        	),
        		 
        	'edit-geo-pos' => array(
        		'type'    => 'Segment',
        		'options' => array(
        			'route'    => '/geo-pos/edit[/:id]',
        			'defaults' => array(
        				'controller' => 'geo-posicionamento',
        				'action'     => 'edit',
        			),
        		),
        	),
        		
        	'delete-geo-pos' => array(
        		'type'    => 'Segment',
        		'options' => array(
        			'route'    => '/geo-pos/delete[/:id]',
        			'defaults' => array(
        				'controller' => 'geo-posicionamento',
        				'action'     => 'delete',
        			),
        		),
        	),
        ),
    ),
	'navigation' => array(
		'default' => array(
			array(
				'label' => 'Início',
				'route' => 'dashboard',
			),
			array(
				'label' => 'Assinaturas',
				'route' => 'admin',
				'class' => 'dropdown-toggle',
				'pages' => array(
					array(
						'label' => 'Planos',
						'route' => 'plan',
						'action' => 'index',
						'class' => 'dropdown',
					),
					array(
						'label' => 'Assinantes',
						'route' => 'subscribers',
						'action' => 'index',
					),
					array(
						'label' => 'Pagamentos',
						'route' => 'payments',
						'action' => 'index',
					),
				),
			),
		),
	),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
    	'locale' => 'pt_BR',
		'translation_file_patterns' => array(
	    	array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
	'module_layouts' => array(
		'Admin' => 'layout/admin',
	),
	'view_manager' => array(
		'display_not_found_reason' => true,
		'display_exceptions' => true,
		'doctype' => 'HTML5',
		'not_found_template' => 'error/404',
		'exception_template' => 'error/index',
		'template_map' => array(
			'layout/layout' 	=> __DIR__ . '/../view/layout/admin.phtml',
			'sidebar-left'      => __DIR__ . '/../view/partials/sidebar-left.phtml',
			'nav-bar'      		=> __DIR__ . '/../view/partials/nav-bar.phtml',
			'modais-empresa'    => __DIR__ . '/../view/partials/modais-empresa.phtml',
			'admin/index/index'	=> __DIR__ . '/../view/admin/index/index.phtml',
			'error/404' 		=> __DIR__ . '/../view/error/404.phtml',
			'error/index' 		=> __DIR__ . '/../view/error/index.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
	'paths' => array(
		'save_session'	=> $pathSaveSession,
	),
);
