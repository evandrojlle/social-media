<?php
	namespace Rest\Form;
	
	use Zend\Form\Form;
	
	class FriendsForm extends Form{
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
					'name' 			=> 'idFriend',
					'attributes'	=> array(
						'type' 			=> 'text',
						'id'			=> 'idFriend'
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
		}
	}