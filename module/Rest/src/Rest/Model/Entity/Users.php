<?php
	namespace Rest\Model\Entity;
 	
 	use Zend\Db\TableGateway\Feature;
 	use Zend\InputFilter\InputFilter;
 	use Zend\InputFilter\Factory as InputFactory;
 	use Zend\InputFilter\InputFilterInterface;
 	use Zend\Db\Sql\Select;
 	
	use Rest\Model\Master\Model;
	use Rest\Library\Common\Common;
		 	
 	class Users extends Model{
	 	protected  $_id_user;
	 	
	 	protected  $_ds_user;
	 	
	 	protected  $_ds_login;

	 	protected  $_ds_pass;

	 	protected  $_st_user;
	 
	 	protected  $_dt_insert;
	 
	 	protected  $_dt_update;
	 	
	 	protected $_inputFilter;
 	
	 	public function __construct(){
	 		$this->table = 'sm_users';
			$this->featureSet = new Feature\FeatureSet();
			$this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
			$this->initialize();
		}
	 	
	 	/**
	 	 * Popula a partir do array.
	 	 *
	 	 * @param array $data
	 	 */
	 	public function populate($pData){
	 		$this->_id_user 	= (!empty($pData['idUser']))	? $pData['idUser']		: self::ZERO;
	 		$this->_ds_user		= (!empty($pData['ds_user']))	? $pData['ds_user'] 	: self::DATA_EMPTY;
	 		$this->_ds_login	= (!empty($pData['ds_login']))	? $pData['ds_login'] 	: self::DATA_EMPTY;
	 		$this->_ds_pass		= (!empty($pData['ds_pass']))	? $pData['ds_pass'] 	: self::DATA_EMPTY;
			$this->_st_user		= (!empty($pData['st_user']))	? $pData['st_user'] 	: self::ONE;
			$this->_dt_insert	= (!empty($pData['dt_insert']))	? $pData['dt_insert']	: Common::getDateNow();
			$this->_dt_update	= (!empty($pData['dt_update']))	? $pData['dt_update']	: Common::getDateZero();
		}
		
		public function _getQuery(){
			$select = new Select();
			$select->from(array('u' => $this->table));

			return $select;
		}
		
		public function fetchAll(){
			$select = $this->_getQuery();
            $resultSet = $this->selectWith($select);
			
			return $resultSet;
		}

        public function getById($pId){
			$this->setId((int) $pId);
			$select = $this->_getQuery();
			$select->where(array('idUser' => $this->getId()));
			
			$resultRow = $this->selectWith($select);
			
			$row = $resultRow->current();
			if (!$row) {
				return false;
			}
			return $row;
		}
		
		public function getActives(){
			$select = $this->_getQuery();
            $select->where(array('st_user' => self::ONE));
			$resultSet = $this->selectWith($select);
            $rowSet = array();
            foreach($resultSet as $rows){
                $rowSet[] = $rows->getArrayCopy();
            }
			return $rowSet;
		}
		
		public function saveUser(){
			$id = (int)$this->_id_user;
			$data = array(
				'ds_user'		=> $this->_ds_user,
				'ds_login'		=> $this->_ds_login,
				'ds_pass'		=> $this->_ds_pass,
				'st_user'		=> $this->_st_user,
				'dt_insert'		=> $this->_dt_insert,
				'dt_update'		=> $this->_dt_update,
			);
			
			if($id == self::ZERO){
				$this->insert($data);
				return $this->lastInsertValue;
			}
			else{
				if($this->getById($id)){
					return $this->update($data, array('idUser' => $id));
				}
				else{
					throw new \Exception('Não foi possível inserir o usuário.');
				}
			}
		}
		
		public function deleteUser($pId){
			$data = array(
				'st_user'		=> self::ZERO,
				'dt_update'		=> Common::getDateNow(),
			);
			return $this->update($data, array('idUser' => (int) $pId));
		}
	 
		public function setInputFilter(InputFilterInterface $pInputFilter){
			throw new \Exception("Não Implementado");
		}
		
	 	public function getInputFilter(){
	 		if(!$this->_inputFilter) {
	 			$inputFilter = new InputFilter();	
	 			$factory = new InputFactory();
	 			$inputFilter->add(
	 				$factory->createInput(
						array(
							'name'       => 'idUser',
							'required'   => false,
							'filters' => array(
								array('name' => 'Int'),
							),
						)
					)
	 			);
	 			$inputFilter->add(
					$factory->createInput(
						array(
							'name'		=> 'ds_user',
							'required'	=> true,
							'filters'  	=> array(
								array('name' => 'StripTags'),
								array('name' => 'StringTrim'),
							),
							'validators' => array(
								array(
									'name' =>'NotEmpty',
									'options' => array(
										'messages' => array(
											\Zend\Validator\NotEmpty::IS_EMPTY => 'Campo "Nome" não pode ser vazio!'
										),
									),
								),
								array(
									'name'    => 'StringLength',
									'options' => array(
										'encoding' => 'UTF-8',
										'max'      => 180,
										'messages' => array(
											'stringLengthTooLong' => 'Campo "Nome" deve conter no máximo 180 caracteres!'
										),
									),
								),
							),
						)
					)
				);
	 			$inputFilter->add(
					$factory->createInput(
						array(
							'name'		=> 'ds_login',
							'required'	=> true,
							'filters'  	=> array(
								array('name' => 'StripTags'),
								array('name' => 'StringTrim'),
							),
							'validators' => array(
								array(
									'name' =>'NotEmpty',
									'options' => array(
										'messages' => array(
											\Zend\Validator\NotEmpty::IS_EMPTY => 'Campo "Login" não pode ser vazio!'
										),
									),
								),
								array(
									'name'    => 'StringLength',
									'options' => array(
										'encoding' => 'UTF-8',
										'max'      => 120,
										'messages' => array(
											'stringLengthTooLong' => 'Campo "Login" deve conter no máximo 120 caracteres!'
										),
									),
								),
							),
						)
					)
				);
	 			$inputFilter->add(
					$factory->createInput(
						array(
							'name'		=> 'ds_pass',
							'required'	=> true,
							'filters'  	=> array(
								array('name' => 'StripTags'),
								array('name' => 'StringTrim'),
							),
							'validators' => array(
								array(
									'name' =>'NotEmpty',
									'options' => array(
										'messages' => array(
											\Zend\Validator\NotEmpty::IS_EMPTY => 'Campo "Senha" não pode ser vazio!'
										),
									),
								),
								array(
									'name'    => 'StringLength',
									'options' => array(
										'encoding' => 'UTF-8',
										'max'      => 32,
										'messages' => array(
											'stringLengthTooLong' => 'Campo "Senha" deve conter no máximo 32 caracteres!'
										),
									),
								),
							),
						)
					)
				);
				$this->_inputFilter = $inputFilter;
	 		}
	 			
	 		return $this->_inputFilter;
	 	}
	 	
	 }