<?php
	namespace Rest\Form;
	
	use Zend\Form\Form;
	
	class FeedsForm extends Form{
		public function __construct($name = null){
			parent::__construct($name);
			$this->setAttribute('method', 'post');
			$this->add(
				array(
					'name' 			=> 'idFeed',
					'attributes'	=> array(
						'type'	=> 'hidden',
						'id'	=> 'idFeed'
					),
				)
			);
			$this->add(
				array(
					'name' 			=> 'ds_feed',
					'attributes'	=> array(
						'type' 			=> 'text',
						'id'			=> 'ds_feed'
					)
				)
			);
			$this->add(
				array(
					'name' 			=> 'st_feed',
					'attributes'	=> array(
						'type' 	=> 'hidden',
						'id'	=> 'st_feed',
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