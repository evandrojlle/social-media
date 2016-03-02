<?php
	namespace Rest\Restful\Master;
	
	use Rest\Model\Entity\Users;
	use Rest\Model\Entity\Auth;
	use Zend\Mvc\Controller\AbstractRestfulController;
	use Zend\EventManager\EventManagerInterface;
	use Zend\Db\Adapter\Adapter as EntityManager;
	use Zend\Authentication\Result;
	use Admin\Library\Common\Common;
	use Admin\Library\MobileDetect\MobileDetect;
		
	class RestfulController extends AbstractRestfulController{
		const _Empty = '';
		
		protected $_arr_messages = array();
		
		protected $_entity_manager;
    	
		protected $_storage;
		
		protected $_path;
		
		protected $_file;
		
		protected $_auth_service;
		
		protected $_database_name_default;
		
		public function getIdSession($pToken, $pHashDevice = null){
			$model = new Auth();
			$fetchRow = $model->getByTokenEDevice($pToken, $pHashDevice);
			if($fetchRow){
				return $fetchRow->id;
			}
			return false;
		}
		
		public function getSessionOnDB($pToken, $pHashDevice = null){
			$model = new Auth();
			$fetchRow = $model->getByTokenAndDevice(stripslashes($pToken), $pHashDevice);
			if($fetchRow){
				return json_decode($fetchRow->ds_json_data);
			}
			return false;	
		}
		
		public function sessionVerify($pToken, $pHashDevice = null){
			$session = $this->getSessionOnDB($pToken, $pHashDevice);
			if(!$session){
				$arrResponse['success'] = false;
				$arrResponse['messages'] = array(
					'message'	=> 'Sessão invalida! Para continuar, faça login novamente.',
					'code' 		=> '999'
				);
			
				$je = json_encode($arrResponse);
				$response = $this->getResponseWithHeader()->setContent($je);
			
				return $response;
			}
			
			return true;
		}
		
		public function accessVerify($pSession){
			if(!isset($pSession->idCustomer)){
				$arrResponse['success'] = false;
				$arrResponse['messages'] = array(
					'message'	=> 'Você não realizou o acesso a esta Empresa. Selecione uma Empresa para prosseguir.',
					'code' 		=> '2'
				);
					
				$je = json_encode($arrResponse);
				$response = $this->getResponseWithHeader()->setContent($je);
					
				return $response->getContent();
			}
			else{
				$model 		= new \Rest\Model\Entity\Users($this->getEntityManager());
				$fetchRow 	= $model->getByIdCustomerAndDsEmail($pSession->idCustomer, $pSession->ds_email);
				$idCustomer = (isset($fetchRow->idCustomer)) ? (int)$fetchRow->idCustomer : \Rest\Model\Entity\Users::ZERO;
				$status 	= (isset($fetchRow->st_access_customer)) ? $fetchRow->st_access_customer : \Rest\Model\Entity\Users::DENIED;
				if(!$fetchRow){
					$arrResponse['success'] = false;
					$arrResponse['messages'] = array(
						'message'	=> 'Você não tem permissão de acesso a esta empresa. Selecione uma empresa e solicite acesso a esta empresa.',
						'code' 		=> '2'
					);
					
					$je = json_encode($arrResponse);
					$response = $this->getResponseWithHeader()->setContent($je);
					
					return $response;
				}
				
				if($status === \Rest\Model\Entity\Users::PENDING){
					$arrResponse['success'] = false;
					$arrResponse['messages'] = array(
						'message'	=> 'Seu acesso a esta empresa ainda não foi liberado. Contate seu gestor.',
						'code' 		=> '2'
					);
						
					$je = json_encode($arrResponse);
					$response = $this->getResponseWithHeader()->setContent($je);
						
					return $response;
				}
				elseif($status === \Rest\Model\Entity\Users::DENIED){
					$arrResponse['success'] = false;
					$arrResponse['messages'] = array(
						'message'	=> 'Seu acesso a esta empresa foi negada. Contate seu gestor.',
						'code' 		=> '2'
					);
				
					$je = json_encode($arrResponse);
					$response = $this->getResponseWithHeader()->setContent($je);
				
					return $response;					
				}
				
				if((int)$pSession->idCustomer !== $idCustomer){
					$arrResponse['success'] = false;
					$arrResponse['messages'] = array(
						'message'	=> 'Você não tem permissão de acesso a esta empresa. Contate o seu gestor.',
						'code' 		=> '2'
					);
			
					$je = json_encode($arrResponse);
					$response = $this->getResponseWithHeader()->setContent($je);
			
					return $response;
				}
			}
			return true;
		}
		
		protected function getAuthService(){
			if(!$this->_auth_service)
				$this->_auth_service = $this->getServiceLocator()->get('AuthService');
		
			return $this->_auth_service;
		}
		
		protected function getSessionStorage(){
			if(!$this->_storage)
				$this->_storage = $this->getServiceLocator()->get('Rest\Model\Entity\AuthStorage');
		
			return $this->_storage;
		}
		
		protected function setStorage($pData){
			$storage = new \Zend\Session\Storage\ArrayStorage(array('danone' => $pData));
			$this->getServiceLocator()->get('Zend\Session\SessionManager')->setStorage($storage);
		}
		
		protected function getStorage(){
			$session = $this->getServiceLocator()->get('AuthService')->getStorage()->getSession();
			return $session;
		}
		
		protected function authServiceValidate($pSiglaPermission = 'ADM'){
			if(!$this->getServiceLocator()->get('AuthService')->hasIdentity()){
				return $this->redirect()->toRoute('sair-adm');
    		}
    		else{
    			$modelPerfil = $this->getServiceLocator()->get('Perfil');
    			$fetchRowPerfil = $modelPerfil->getBySigla($pSiglaPermission);
    			if($fetchRowPerfil){
	    			$storage = $this->getStorage();
	    			if($storage['idPerfil'] != $fetchRowPerfil->idPerfil){
	    				$userLogged = $storage['login'];
		    			$res = $this->readSession($userLogged);
		    			if(!$res){
		    				$this->flashmessenger()->addMessage('Sua sessão expirou');
		    				return $this->redirect()->toRoute('sair-adm');
		    			}
	    				return $this->redirect()->toRoute('permission-denied');		    				
	    			}
    			}
    			else 
    				return $this->redirect()->toRoute('permission-denied');
    			
    		}
		}
		
	    protected function setEntityManager(EntityManager $pEntityManager){
	    	$this->_entity_manager = $pEntityManager;
	    }
	    
	    protected function getEntityManager(){
	    	if (null === $this->_entity_manager){
	    		$this->_entity_manager = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
	    	}
	    	return $this->_entity_manager;
	    }
	    
	    protected function getEntityManagerSec(){
	    	$params = $this->params()->fromQuery();
	    	$request = $this->getRequest();
	    	if($params && (isset($params['id']))){
	    		if(!isset($params['token'])){;
	    			$arrKey = array('id', 'token', 'hash_device');
	    			$arrParams = explode(';', $params['id']);
	    		
	    			$params = array_combine($arrKey, $arrParams);
	    		}
	    	}
	    	elseif(!isset($params['token']) && $request->isPost()){
		    	$post = $request->getPost();
	    		$params = array(
	    			'id' 			=> $post->get('idPacienteAvaliacao'),
	    			'token' 		=> $post->get('token'),
	    			'hash_device'	=> null,
	    		);	    		
	    	}
	    	
	    	$arrConn = array(
    			'driver' 			=> '',
    			'dsn'				=> '',
    			'driver_options'	=> '',
    			'username'			=> '',
    			'password'			=> '',
	    	);
	    	
	    	$config = $this->getServiceLocator()->get('Config');
	    	$dbDefault = $config['db'];
	    	
	    	
	    	$adapterDefault	= new \Zend\Db\Adapter\Adapter($dbDefault);
	    	$modelSession = new \Admin\Model\Entity\Auth($adapterDefault);
	    	
	    	$um = substr($dbDefault['dsn'], (strpos($dbDefault['dsn'], '=') + 1));
	    	$this->setDatabaseNameDefault(substr($um, 0, strpos($um, ';'))); 
	    	
	    	$fetchRowSession 		= $modelSession->getByToken($params['token']);
	    	if($fetchRowSession){	   
	    		$jd = json_decode($fetchRowSession->data);
	    		if(isset($jd->idInstituicao)){
	    			$idInstituicao = $jd->idInstituicao;
	    		}
	    		elseif(isset($params['idInstituicao'])){
	    			$idInstituicao = $params['idInstituicao'];
	    		}
	    		else{
	    			$idInstituicao = 0;
	    		}
	    		
	    		$modelCfgInstituicao	= new \Admin\Model\Entity\ConfiguracaoInstituicao($adapterDefault);
	    		$fetchRow = $modelCfgInstituicao->getById($idInstituicao);
	    		if($fetchRow){
	    			$arrConn['driver'] 			= $fetchRow->ds_driver;
	    			$arrConn['dsn'] 			= $fetchRow->ds_alias . ':dbname=' . $fetchRow->ds_database_name . ';host=' . $fetchRow->ds_host_name;
	    			//$arrConn['driver_options']	= $fetchRow->ds_driver_options;
	    			$arrConn['driver_options']	= Array('1002' => 'SET NAMES \'UTF8\'');
	    			$arrConn['username'] 		= $fetchRow->ds_usuario_banco;
	    			$arrConn['password'] 		= $fetchRow->ds_senha_banco;
	    		}	    	
	    	}
	    		
	    	return new \Zend\Db\Adapter\Adapter($arrConn);
	    }
	    
	    public function setDatabaseNameDefault($pDatabaseName){
	    	$this->_database_name_default = $pDatabaseName;
	    }
	    
	    public function getDatabaseNameDefault(){
	    	return $this->_database_name_default;
	    }
	    
	    protected function getErrorMessage($code = null){
	    	switch($code){
	    		case Result::FAILURE:								// 0
	    			$message = 'Falha ao tentar realizar a autenticação!';
	    			break;
	    			 
	    		case Result::FAILURE_IDENTITY_NOT_FOUND:			// -1
	    			$message = 'Usuário não Localizado!';
	    			break;
	    			 
	    		case Result::FAILURE_IDENTITY_AMBIGUOUS:			// -2
	    			$message = 'Usuário Ambíguo.';
	    			break;
	    			 
	    		case Result::FAILURE_CREDENTIAL_INVALID:			// -3
	    			$message = 'Senha inválida';
	    			break;
	    			 
	    		case Result::FAILURE_UNCATEGORIZED:					// -4
	    			$message = 'Falha por razães nao categorizada';
	    			break;
	    			 
	    		default:
	    			$message = 'Usuário ou senha inválidos!';
	    	}
	    	 
	    	return $message;
	    }
	    
		protected function getConfig(){
			return $this->getServiceLocator()->get('Application')->getConfig();
		}
		
		protected function getNameRoutes(){
			$cfg = $this->getConfig();
			$arrAct = array();
			foreach($cfg['router']['routes'] as $key => $val){
				$arrAct[$key] = ucwords(str_replace("-", " ", $key));
			}
			ksort($arrAct);
				
			return $arrAct;
		}
		
		protected function getNameRoutesByString($string){
			$cfg = $this->getConfig();
			$arrAct = array();
			foreach($cfg['router']['routes'] as $key => $val){
				if(strpos($key, $string))
					$arrAct[$key] = ucwords(str_replace("-", " ", $key));
			}
			ksort($arrAct);
				
			return $arrAct;
		}
		
		protected function getMatchedRouteName(){
    		return $this->getEvent()->getRouteMatch()->getMatchedRouteName();
    	}
    	
    	protected function openSessionFile($login, $mode){
    		$cfg = $this->getConfig();
    		$this->_path = $cfg['paths']['save_session'];
    		$this->_file = 'session_' . $login . '.ini';
    		$handle = fopen($this->_path . $this->_file, $mode);
    		
    		return $handle;
    	}
    	
    	protected function saveSession($login){
    		$str = date('Y-m-d') . ' = ' . date('H:i:s');
    		$handle = $this->openSessionFile($login, "w+");
    		$response = fwrite($handle, $str);
    		fclose ($handle);
    		
    		if($response)
    			return true;
    		
    		return false;
    	
    	}
    	
    	protected function readSession($login){
    		$handle = $this->openSessionFile($login, "rb");
    		$contentFile = fread($handle, filesize($this->_path . $this->_file));
    		fclose ($handle);
    		
    		$arrContent = explode(' = ', $contentFile);
    		$dateLogged = $arrContent[0] . ' ' . $arrContent[1];
    		$now = date('Y-m-d H:i:s');
    		$diffInMinutes = ((strtotime($now) - strtotime($dateLogged)) / 60);
    		
    		if($arrContent[0] === date("Y-m-d"))
    			if(ceil($diffInMinutes) <= 30){
    				$this->saveSession($login);
    				return true;
    			}
    		return false;
    		
    	}
    	
    	protected function saveTabEmpresasNaSessao($tabName = null){
    		if(null !== $tabName){
    			$session = $this->getServiceLocator()->get('AuthService')->getStorage()->getSession();
    			$refresh = array_merge($session, array('tab'=> $tabName));
    			$this->getServiceLocator()->get('AuthService')->getStorage()->write($refresh);
    		}
    		
    		$refreshed = $this->getServiceLocator()->get('AuthService')->getStorage()->getSession();
    		return $refreshed;
    	}
    	
    	protected function saveLog($pIdEmpresa, $pData){
    		$storage = $this->getStorage();
    		$modelUsuario = new \Admin\Model\Entity\Usuario;
    		$rowSet = $modelUsuario->select(array('idAcesso' => $storage['idAcesso']));
    		$idUsuario = $rowSet->current()->idUsuario;
    		
    		$arrData['idLog'] 				= 0;
    		$arrData['dt_log'] 				= date('Y-m-d H:i:s');
    		$arrData['ds_ip_log'] 			= \Admin\Library\Common\Common::getIpAtual();
    		$arrData['idUsuario'] 			= $idUsuario;
    		$arrData['idEmpresa'] 			= $pIdEmpresa;
    		$arrData['ds_descricao_log']	= $pData;
    		
    		$modelLog = new \Admin\Model\Entity\LogEmpresa;
    		$modelLog->populate($arrData);
    		$modelLog->saveLog();
    		
    		return $modelLog->getLastInsertValue();
    	}
    	
    	protected function mensageria($pMessage, $pCode){
    		$this->_arr_messages[] = array('message' => $pMessage, 'code' => $pCode);
    	}
    	
    	// configure response
    	public function getResponseWithHeader(){
    		$response = $this->getResponse();
    		$response->getHeaders()
    		//make can accessed by *
    		->addHeaderLine('Access-Control-Allow-Origin','*')
    		//set allow methods
    		->addHeaderLine('Access-Control-Allow-Methods', 'POST PUT DELETE GET');
    			
    		return $response;
    	}
    	
    	protected function setResponse($pResponse){
    		$json = json_encode($pResponse);
    		$response = $this->getResponseWithHeader()->setContent($json);
    		return $response;
    	}
    	
    	/**
    	 * Função para gerar senhas aleatórias
    	 *
    	 * @param integer $tamanho Tamanho da senha a ser gerada
    	 * @param boolean $maiusculas Se terá letras maiúsculas
    	 * @param boolean $numeros Se terá números
    	 * @param boolean $simbolos Se terá símbolos
    	 *
    	 * @return string A senha gerada
    	 */
    	protected function geraSenha($pTamanho = 8, $pMaiusculos = true, $pNumeros = true, $pSimbolos = false){
    		$minusculos = 'abcdefghijklmnopqrstuvwxyz';
    		$maiusculos = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    		$numerico 	= '1234567890';
    		$simbolos 	= '!@#$%*-';
    		$caracteres	= '';
    		$retorno 	= '';
    		 
    		$caracteres .= $minusculos;
    	
    		if($pMaiusculos)
    			$caracteres .= $maiusculos;
    	
    		if($pNumeros)
    			$caracteres .= $numerico;
    	
    		if ($pSimbolos)
    			$caracteres .= $simbolos;
    		 
    		$strLen = strlen($caracteres);
    		for($n = 1; $n <= $pTamanho; $n++){
    			$rand = mt_rand(1, $strLen);
    			$retorno .= $caracteres[$rand-1];
    		}
    		return $retorno;
    	}
	}