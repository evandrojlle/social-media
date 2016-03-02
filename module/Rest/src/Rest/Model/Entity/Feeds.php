<?php
	namespace Rest\Model\Entity;
 	
 	//use Zend\Db\Adapter\Adapter;
 	//use Zend\Db\TableGateway\AbstractTableGateway;
 	use Zend\Db\TableGateway\Feature;
 	use Zend\InputFilter\InputFilter;
 	use Zend\InputFilter\Factory as InputFactory;
 	//use Zend\InputFilter\InputFilterAwareInterface;
 	use Zend\InputFilter\InputFilterInterface;
 	use Zend\Db\Sql\Select;
 	
	use Rest\Model\Master\Model;
	use Rest\Library\Common\Common;
		 	
 	class Feeds extends Model{
	 	protected  $_id_feed;
	 	
	 	protected  $_id_user;

	 	protected  $_ds_feed;
	 	
	 	protected  $_st_feed;
	 
	 	protected  $_dt_insert;
	 
	 	protected  $_dt_update;
	 	
	 	protected $_inputFilter;
 	
	 	public function __construct(){
	 		$this->table = 'sm_feeds';
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
	 		$this->_id_feed 	= (!empty($pData['idFeed']))	? $pData['idFeed']		: self::ZERO;
	 		$this->_id_user 	= (!empty($pData['idUser']))	? $pData['idUser']		: self::ZERO;
	 		$this->_ds_feed		= (!empty($pData['ds_feed']))	? $pData['ds_feed'] 	: self::DATA_EMPTY;
			$this->_st_feed		= (!empty($pData['st_feed']))	? $pData['st_feed'] 	: self::ONE;
			$this->_dt_insert	= (!empty($pData['dt_insert']))	? $pData['dt_insert']	: Common::getDateNow();
			$this->_dt_update	= (!empty($pData['dt_update']))	? $pData['dt_update']	: Common::getDateZero();
		}
		
		public function _getQuery(){
			$select = new Select();
			$select->from(array('f' => $this->table));
			$select->join(array('u' => 'sm_users'), 'u.idUser = f.iduser', array('ds_user', 'ds_login'));
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
			$select->where(array('idFeed' => $this->getId()));
			$resultRow = $this->selectWith($select);
			
			$row = $resultRow->current();
			if (!$row) {
				return false;
			}
			return $row;
		}

        public function getByIdFeedAndIdUser($pIdFeed, $pIdUser){
			$this->setId((int) $pIdFeed);
			$this->setIdUser((int) $pIdUser);
			$select = $this->_getQuery();
			$select->where(array('f.idFeed' => $this->getId(), 'u.idUser' => $this->getIdUser()));
			$resultRow = $this->selectWith($select);

			$row = $resultRow->current();
			if (!$row) {
				return false;
			}
			return $row;
		}

		public function getByIdUser($pIdUser){
			$this->setId((int) $pIdUser);
			$select = $this->_getQuery();
			$select->where(array('f.idUser' => $this->getId()));
			$resultSet = $this->selectWith($select);
			$resultRows = array();
			foreach($resultSet as $rows)
				$resultRows[] = $rows->getArrayCopy();

			return $resultRows;
		}
		
		public function getActives(){
			$select = $this->_getQuery();
            $select->where(array('st_feed' => self::ONE));
			$resultSet = $this->selectWith($select);
            $rowSet = array();
            foreach($resultSet as $rows){
                $rowSet[] = $rows->getArrayCopy();
            }
			return $rowSet;
		}
		
		public function saveFeed(){
			$id = (int)$this->_id_feed;
			$data = array(
				'idUser'		=> $this->_id_user,
				'ds_feed'		=> $this->_ds_feed,
				'st_feed'		=> $this->_st_feed,
				'dt_insert'		=> $this->_dt_insert,
				'dt_update'		=> $this->_dt_update,
			);
			
			if($id == self::ZERO){
				$this->insert($data);
				return $this->lastInsertValue;
			}
			else{
				if($this->getById($id)){
					return $this->update($data, array('idFeed' => $id));
				}
				else{
					throw new \Exception('Não foi possível inserir o registro.');
				}
			}
		}
		
		public function deleteFeed($pId){
			return $this->delete(array('idFeed' => (int) $pId));
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
							'name'       => 'idFeed',
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
							'name'		=> 'idUser',
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
											\Zend\Validator\NotEmpty::IS_EMPTY => 'Campo "Usuário" não pode ser vazio!'
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
							'name'		=> 'ds_feed',
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
											\Zend\Validator\NotEmpty::IS_EMPTY => 'Campo "Feed" não pode ser vazio!'
										),
									),
								),
								array(
									'name'    => 'StringLength',
									'options' => array(
										'encoding' => 'UTF-8',
										'max'      => 140,
										'messages' => array(
											'stringLengthTooLong' => 'Campo "Feed" deve conter no máximo 140 caracteres!'
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