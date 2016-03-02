<?php
	namespace Rest\Model\Entity;
 	
 	use Zend\Db\TableGateway\Feature;
 	use Zend\InputFilter\InputFilter;
 	use Zend\InputFilter\Factory as InputFactory;
 	use Zend\InputFilter\InputFilterInterface;
 	use Zend\Db\Sql\Select;
 	
	use Rest\Model\Master\Model;
	use Rest\Library\Common\Common;
		 	
 	class Friends extends Model{
	 	protected  $_id_user;
	 	
	 	protected  $_id_friend;
	 	
	 	protected  $_dt_insert;
	 
	 	protected $_inputFilter;
 	
	 	public function __construct(){
	 		$this->table = 'sm_friends';
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
	 		$this->_id_friend	= (!empty($pData['idFriend']))	? $pData['idFriend'] 	: self::ZERO;
	 		$this->_dt_insert	= (!empty($pData['dt_insert']))	? $pData['dt_insert']	: Common::getDateNow();
		}
		
		public function _getQuery(){
			$select = new Select();
			$select->from(array('f' => $this->table));
            $select->columns(array('idUser', 'idFriend'));
            $select->join(array('u' => 'sm_users'), 'u.idUser = f.idUser', array('ds_user', 'ds_login_user' => 'ds_login'));
            $select->join(array('a' => 'sm_users'), 'a.idUser = f.idFriend', array('ds_friend' => 'ds_user', 'ds_login_friend' => 'ds_login'));
			return $select;
		}
		
		public function fetchAll(){
			$select = $this->_getQuery();
            $resultSet = $this->selectWith($select);
			
			return $resultSet;
		}

        public function getById($pId){
			$this->setIdFriend((int) $pId);
			$select = $this->_getQuery();
            $select->where(array('idFriend' => $this->getIdFriend()));
			
			$resultRow = $this->selectWith($select);
			
			$row = $resultRow->current();
			if (!$row) {
				return false;
			}
			return $row;
		}

        public function getByIdUser($pIdUser){
			$this->setIdUser((int) $pIdUser);
			$select = $this->_getQuery();
			$select->where(array('f.idUser' => $this->getIdUser()));

			$resultSet = $this->selectWith($select);
			$rowSet = array();
			foreach($resultSet as $rows)
				$rowSet[] = $rows->getArrayCopy();

			return $rowSet;
		}

        public function getByIdUserAndIdFriend($pIdUser, $pIdFriend){
            $this->setIdUser((int) $pIdUser);
            $this->setIdFriend((int) $pIdFriend);
            $select = $this->_getQuery();
            $select->where(array('idFriend' => $this->getIdFriend(), 'u.idUser' => $this->getIdUser()));

            $resultRow = $this->selectWith($select);

            $row = $resultRow->current();
            if (!$row) {
                return false;
            }
            return $row;
        }

        public function saveFriend(){
			$data = array(
				'idUser'		=> $this->_id_user,
				'idFriend'		=> $this->_id_friend,
				'dt_insert'		=> $this->_dt_insert,
			);

            $fetchRow = $this->getByIdUserAndIdFriend($this->_id_user, $this->_id_friend);
            if($fetchRow)
                return 2;
            else{
                if($this->insert($data))
                    return 1;
                else
                    return 0;
            }
		}
		
		public function deleteFriend($pIdUser, $pIdFriend){
			return $this->delete(array('idUser' => (int)$pIdUser, 'idFriend' => (int)$pIdFriend));
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
							'name'       => 'idFriend',
							'required'   => false,
							'filters' => array(
								array('name' => 'Int'),
							),
						)
					)
	 			);
				$this->_inputFilter = $inputFilter;
	 		}
	 			
	 		return $this->_inputFilter;
	 	}
	 	
	 }