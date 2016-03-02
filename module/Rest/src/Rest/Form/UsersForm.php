<?php
	namespace Rest\Form;
	
	use Zend\Form\Form;
	
	class UsersForm extends Form{
		public function __construct($name = null){
			parent::__construct($name);
			$this->setAttribute('method', 'post');
			$this->add(
				array(
					'name' 			=> 'idUser',
					'attributes'	=> array(
						'type'	=> 'hidden',
						'id'	=> 'idUser'
					),
				)
			);
			$this->add(
				array(
					'name' 			=> 'ds_user',
					'attributes'	=> array(
						'type' 			=> 'text',
						'id'			=> 'ds_user'
					)
				)
			);
			$this->add(
				array(
					'name' 			=> 'ds_login',
					'attributes'	=> array(
						'type' 			=> 'text',
						'id'			=> 'ds_login'
					)
				)
			);
			$this->add(
				array(
					'name' 			=> 'ds_pass',
					'attributes'	=> array(
						'type' 			=> 'text',
						'id'			=> 'ds_pass'
					)
				)
			);
			$this->add(
				array(
					'name' 			=> 'st_user',
					'attributes'	=> array(
						'type' 	=> 'hidden',
						'id'	=> 'st_user',
					)
				)
			);
			$this->add(
				array(
					'name' 			=> 'dt_insert',
					'attributes'	=> array(
						'type'	=> 'hidden',
						'id'	=> 'dt_insert',
					)
				)
			);
			$this->add(
				array(
					'name' 			=> 'dt_update',
					'attributes'	=> array(
						'type'	=> 'hidden',
						'id'	=> 'dt_update',
					)
				)
			);
		}
	}