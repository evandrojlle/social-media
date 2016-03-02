<?php
	namespace Rest\Model\Master;
	
	use Zend\InputFilter\InputFilter;
	use Zend\InputFilter\Factory as InputFactory;
	use Zend\InputFilter\InputFilterAwareInterface;
	use Zend\InputFilter\InputFilterInterface;
	
	use Zend\Db\Adapter\Adapter;
	use Zend\Db\TableGateway\AbstractTableGateway;
	use Zend\Db\TableGateway\Feature;
	
	class Model extends AbstractTableGateway implements InputFilterAwareInterface{
		const ACTIVE 		= 'ACTIVE'; 				//ATIVO
		
		const ADM 			= 'ADM'; 					//ADM
		
		const ADMINISTRATOR = 'ADMINISTRATOR'; 			//ADMINISTRADOR
		
		const ALL			= 'ALL';					//TODOS
		
		const ASC			= 'ASC';					//ASCENDENTE
		
		const ASSESSING		= 'ASSESSING';				//AVALIANDO
		
		const BLOCKING 		= 'BLOCKING';				//BLOQUEANDO
		
		const CANCELED 		= 'CANCELED';				//CANCELADO
		
		const COMPLETED 	= 'COMPLETED';				//COMPLETO/CONCLUÍDO
		
		const COMPLETING	= 'COMPLETING';				//COMPLETANDO/CONCLUÍNDO
		
		const CREATING		= 'CREATING';				//CRIANDO
			
		const CREATED		= 'CREATED';				//CRIADO
			
		const DATA_EMPTY	= '';						//VAZIO
			
		const DELETED 		= 'DELETED';				//EXCLUÍDO
			
		const DELETING 		= 'DELETING';				//EXCLUÍNDO
			
		const DESC			= 'DESC';					//DESCENDENTE
			
		const EDITED 		= 'EDITED';					//EDITADO
			
		const EDITING 		= 'EDITING';				//EDITANDO
			
		const FILED			= 'FILED';					//ARQUIVADO
		
		const INACTIVE 		= 'INACTIVE';				//INATIVO
			
		const LOCKED 		= 'LOCKED';					//BLOQUEADO
			
		const MEASURED 		= 'MEASURED';				//AVALIADO
		
		const MEASURER 		= 'MEASURER';				//AVALIADOR
		
		const DENIED 		= 'DENIED';					//NEGADO
		
		const NO 			= 'NO';						//NÃO
		
		const NULLABLE		= null;						//NULO
		
		const PARTICIPATE	= 'PARTICIPATE'; 			//PARTICIPO
		
		const PARTICIPANT	= 'PARTICIPANT';			//PARTICIPANTE
		
		const WAITING		= 'WAITING';				//AGUARDANDO
			
		const PENDING 		= 'PENDING';				//PENDENTE
			
		const REVALUATION 	= 'REVALUATION';			//REAVALIAÇÃO
		
		const REVALUED 		= 'REVALUED';				//REAVALIADO
			
		const RENEWED 		= 'RENEWED';				//RENOVADO
			
		const REVISION 		= 'REVISION';				//REVISÃO
		
		const YES 			= 'YES';					//SIM
		
		const ZERO 			= 0;
		
		const ONE 			= 1;
		
		const TWO 			= 2;
		
		const THREE 		= 3;
		
		const FOUR 			= 4;
		
		const FIVE 			= 5;
		
		const SIX 			= 6;
		
		const SEVEN 		= 7;
		
		const EIGHT 		= 8;
		
		const NINE 			= 9;
		
		protected $inputFilter;
		
		public $_adapter;
		
		protected $_database_name_default;
		
		public function __construct($tableName){
			$this->table = $tableName;
			$this->featureSet = new Feature\FeatureSet();
			$this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
			$this->initialize();
		}
		
		public function setDatabaseNameDefault($pDatabaseName){
			$this->_database_name_default = $pDatabaseName;
		}
		
		public function getDatabaseNameDefault(){
			return $this->_database_name_default;
		}
		
		public function passwordHash($pPassword){
			if($pPassword)
				return md5($pPassword);
				
			return false;
		}
		
		public function passwordVerify($pPassword, $pHash){
			if($pPassword && $pHash)
				if(md5($pPassword) === $pHash)
				return true;
			
			return false;
		}
		
		public function setEmailCodVerify($pStr, $pSalt = null){
			if($pSalt)
				$str = $pStr . $pSalt . date('Y-m-d');
			else 
				$str = $pStr . date('Y-m-d');
			
			return $this->passwordHash($str);
		}
		
		/**
 	 	 * Método Mágico setter para salvar propriedades protegidas.
 	 	 * @param string $pName
 	 	 * @param mixed $pValue
 	 	 */
 		public function __set($pName, $pValue){
			$this->$pName = $pValue;
		}
		
		/**
		 * Método Mágico getter para expor propriedades protegidas.
		 * @param string $pName
		 * @return mixed
		 */
		public function __get($pName){
			return $this->$pName;
		}
		
		/**
		 * Método Mágico call disparado quando invocando métodos inacessíveis em um contexto de objeto..
		 * @param string $pMethod
		 * @param array $pArguments
		 * @return mixed
		 */
		public function __call($pMethod, $pArguments){
			// Selecionando os 3 primeiros caracteres do método chamado.
			$prefix = substr($pMethod, 0, 3);
		
			// Selecionando o restante do método chamado.
			$prop	= substr($pMethod, 3);
		
			// Converte os caracteres maiúsculos por minúsculos, concatenando com underscore.
			$prop 	= preg_replace_callback(
					'/[A-Z]/',
					create_function(
						'$matches',
						'return "_" . strtolower($matches[0]);'
					),
					$prop
			);
			
			// Remove o primeiro underscore.
			if(!$prop)
				$prop = substr($prop, 1);
			
			if($prefix == 'set'){
				$this->$prop = $pArguments[0];
			}
			elseif($prefix == 'get'){
				return $this->$prop;
			}
			else{
				throw new \Exception('O método ' . $pMethod . ' não existe!');
			}
		}
		
		/**
		 * Converte o objeto para array.
		 *
		 * @return array
		 */
		public function getArrayCopy(){
			return get_object_vars($this);
		}
		
		public function setAdapter($pAdapter){
			if(!$this->_adapter)
				$this->_adapter = $pAdapter;	
		}
		
		public function getAdapter(){
			return $this->_adapter;
		}
		
		public function setInputFilter(InputFilterInterface $inputFilter){
			throw new \Exception("Não Implementado");
		}
		
		public function getInputFilter(){}
	}