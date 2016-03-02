<?php
	namespace Rest\Controller\Master;
	
	use Rest\Interfaces\ClientInterface;
    use Zend\Mvc\Controller\AbstractActionController;
	//use Zend\EventManager\EventManagerInterface;
	use Zend\Db\Adapter\Adapter as EntityManager;
	use Zend\Authentication\Result;
	
	use Rest\Library\Common\Common;
	use Rest\Library\MobileDetect\MobileDetect;	
	use Rest\Model\Entity\Auth; 
		
	abstract class ClientController extends AbstractActionController implements ClientInterface{
		const MOBILE 	= 'MOBILE';
		
		const TABLET 	= 'TABLET';
		
		const IPHONE 	= 'IPHONE';
		
		const ANDROID 	= 'ANDROID';
		
		const PC 		= 'PC';
		
		const WINDOWS	= 'WINDOWS';
		
		const LINUX 	= 'LINUX';
		
		const MAC 		= 'MACINTOSH';
		
		const IPAD		= 'IPAD';
		
		protected $_arrMessages = array();
		
		protected $_em;
    	
		protected $_storage;

		protected $_authService;

        public function getIp(){
            $remoteAddr = $_SERVER['REMOTE_ADDR'];
            return str_replace('.', '', $remoteAddr);
        }

        public function getOs(){
            $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
            if(preg_match('/' . self::WINDOWS . '/', $userAgent)){
                $plataforma = self::WINDOWS;
            }
            elseif(preg_match('/' . self::LINUX . '/', $userAgent) && !preg_match('/' . self::ANDROID . '/', $userAgent)){
                $plataforma = self::LINUX;
            }
            elseif(preg_match('/' . self::MAC . '/', $userAgent)){
                $plataforma = self::MAC;
            }
            elseif(preg_match('/' . self::ANDROID . '/', $userAgent)){
                $plataforma = self::ANDROID;
            }
            elseif(preg_match('/' . self::IPHONE . '/', $userAgent)){
                $plataforma = self::IPHONE;
            }
            elseif(preg_match('/' . self::IPAD . '/', $userAgent)){
                $plataforma = self::IPAD;
            }
            else{
                $plataforma = self::LINUX;
            }

            return $plataforma;
        }

		public function mobileDetect(){
			$md = new MobileDetect();
			if($md->isMobile()){
				return self::MOBILE;
			}
			elseif($md->isTablet()){
				return self::TABLET;
			}
			else{
				return self::PC;
			}
		}

		public function getHashDevice(){
			$device = $this->mobileDetect();
			$os = $this->getOs();
			$ip = $this->getIp();
			$hashDevice = md5($device . '_' . $os . '_' . $ip);
			
			return $hashDevice;
		}

       public function restClient($pData, $pRoute){
			try {
				$method 	= $pData['method'];
				$uri 		= Common::baseUrl() . ':80' . $this->getRequest()->getBaseUrl() . '/' . $pRoute;
				$id 		= '';
				foreach($pData as $k => $v){
					if(substr($k, 0, 2) == 'id'){
						$id = $k;
						break;
					}
				}

				$client = new \Zend\Http\Client();
				$client->setAdapter('Zend\Http\Client\Adapter\Curl');
				$client->setUri($uri);
				switch($method) {
					case 'get' :
						if(count($pData) > 2)
							$strId = implode(';', $pData);
						else
							$strId = $pData[$id];
						$arrayId = array('id' => $strId);
						$client->setMethod('GET');
						$client->setParameterGET($arrayId);
						break;
					case 'get-list' :
						$client->setMethod('GET');
						break;
					case 'create' :
						$client->setMethod('POST');
						$client->setParameterPOST($pData);
						$client->setParameterGET($pData);
						break;
					case 'update' :
						$data = $pData;
						$adapter = $client->getAdapter();
						$options = array(
							CURLOPT_FOLLOWLOCATION => true,
						);
						
						$adapter->connect(Common::getHost());
						$adapter->setOptions($options);
						
						$uri = $client->getUri() . "?id={$data[$id]}";
						$arrUrl = new \Zend\Uri\Uri($uri);
						$query_build = http_build_query($data);
						$adapter->write('PUT', $arrUrl, '1.1', array(), $query_build);
							
						$responsecurl = $adapter->read();
						list($headers, $content) = explode("\r\n\r\n", $responsecurl, 2);
						$response = $this->getResponse();
			
						$response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8');
						$response->setContent($content);
			
						return $response;
					case 'delete' :
						$adapter = $client->getAdapter();
						$adapter->connect(Common::getHost());
							
						$uri = $client->getUri() . "?id={$pData[$id]}";
						$arrUrl = new \Zend\Uri\Uri($uri);
						$adapter->write('DELETE', $arrUrl, '1.1', array());
							
						$responsecurl = $adapter->read();
						list($headers, $content) = explode("\r\n\r\n", $responsecurl, 2);
							
						$response = $this->getResponse();
						$response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8');
						$response->setContent($content);
							
						return $response;

				}

                $response = $client->send();

                //Common::debugger($response);
                //Common::debugger($message);
                //die;

                //if get/get-list/create
				if(!$response->isSuccess()){
					$message = array('message' => $response->getStatusCode() . ': ' . $response->getReasonPhrase(), 'code' => '4');
					$env = (!empty(getenv('API_ENV'))) ? getenv('API_ENV') : '';
					if($env === 'development'){
						$message = array(
							'message' 	=> $response->getContent(),
							'code'		=> '4'
						);
					}
					$je = json_encode($message);
					$response = $this->getResponse();
					$response->setContent($je);
					return $response;
				}
				
				$body = $response->getBody();
				
				$response = $this->getResponse();
				$response->setContent($body);
				
				return $response;
				
			}
			catch(\Exception $ex){
				$message = array('message' => $ex->getMessage(), 'code' => $ex->getCode());
				$je = json_encode($message);
					
				$response = $this->getResponse();
				$response->setContent($je);
				return $response;
			}
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
		
		public function getAuthService(){
			if(!$this->_authService)
				$this->_authService = $this->getServiceLocator()->get('AdminAuthService');
		
			return $this->_authService;
		}
		
		public function getSessionStorage(){
			if(!$this->_storage)
				$this->_storage = $this->getServiceLocator()->get('AuthStorage');
		
			return $this->_storage;
		}
		
		public function getStorage(){
			return $this->getServiceLocator()->get('AdminAuthService')->getStorage()->getSession();
		}

	    public function setEntityManager(EntityManager $em){
	    	$this->_em = $em;
	    }
	    
	    public function getEntityManager(){
	    	if (null === $this->_em) {
	    		$this->_em = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
	    	}
	    	return $this->_em;
	    }

	    public function getRelated($id, $class, $method, $column, $em){
			$model = new $class;
			$result = $model->$method($id, $em);
			$arrReturn = array();
			if($result)
				foreach($result as $data)
					$arrReturn[] = $data->$column;
					
			return $arrReturn;
		}
		
		public function getConfig(){
			return $this->getServiceLocator()->get('Application')->getConfig();
		}

        public function getNameRoutes(){
			$cfg = $this->getConfig();
			$arrAct = array();
			foreach($cfg['router']['routes'] as $key => $val){
				$arrAct[$key] = ucwords(str_replace("-", " ", $key));
			}
			ksort($arrAct);
				
			return $arrAct;
		}

        public function getNameRoutesByString($string){
			$cfg = $this->getConfig();
			$arrAct = array();
			foreach($cfg['router']['routes'] as $key => $val){
				if(strpos($key, $string))
					$arrAct[$key] = ucwords(str_replace("-", " ", $key));
			}
			ksort($arrAct);
				
			return $arrAct;
		}
	}