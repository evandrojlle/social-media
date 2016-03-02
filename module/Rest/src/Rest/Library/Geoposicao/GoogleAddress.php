<?php
	namespace Admin\Library\Geoposicao;
	
	use Admin\Library\Common\Common;
	use Zend\Http\Client;
	use Zend\Http\Client\Adapter\Exception\RuntimeException as ClientRuntimeException;
	
	class GoogleAddress{
		protected $_url = 'http://maps.google.com/maps/api/geocode/json?address=%s&sensor=false';
		protected $_client;
		protected $_json;
		protected $_result;
		
		public function __construct($pAddress){
			try {
				Common::allowUrlFopen(true);
            	$url = sprintf($this->_url, urlencode($pAddress));
            	$this->_client = new Client($url);
            	$this->_json = json_decode($this->_client->send()->getBody());
            	$this->_result = (false == $this->_json) ? array('results' => array(), 'status' => 'JSON_ERROR') : $this->_json;
            	Common::allowUrlFopen(false);
        	} 
        	catch (ClientRuntimeException $e) {
            	$this->_result = array('results' => array(), 'status' => 'HTTP_CLIENT_ERROR');
       	 	} 
        	catch (\Exception $e) {
            	$this->_result = array('results' => array(), 'status' => 'UNKNOWN_ERROR');
        	}
		}
		
		public function getAddressComponents(){
			$results = $this->_result->results[0];
			return $results->address_components;
		}
		
		public function getFormattedAddress(){
			$results = $this->_result->results[0];
			return $results->formatted_address;
		}
		
		public function getGeometry(){
			$results = $this->_result->results[0];
			return $results->geometry;
		}
		
		public function getBounds(){
			$geometry = $this->getGeometry();
			return $geometry->bounds;
		}
		
		public function getLocation(){
			$geometry = $this->getGeometry();
			return $geometry->location;
		}
		
		public function getLocationType(){
			$geometry = $this->getGeometry();
			return $geometry->location_type;
		}
		
		public function getViewport(){
			$geometry = $this->getGeometry();
			return $geometry->viewport;
		}
		
		public function getStatus(){
			return $this->_result->status;
		}
		
		public function getUrl(){
			if(null != $this->_url)
				return $this->_url;
			else 
				throw new \Exception('Não foi informada nehuma URL para geoLocalização');
		}
		
		public function setUrl($pUrl){
			$this->_url = $pUrl;
		}
		
		public function getClient(){
			if(null != $this->_client)
				return $this->_client;
			else
				throw new \Exception('Não foi localizado o Client para manipuçlação dos dados.');
		}
		
		public function setClient(Client $pClient){
			if(null != $this->_client)
				$this->_client = $pClient;
		}
		
		public function getJson(){
			return $this->_json;
		}
		
		public function getResult(){
			return $this->_result;
		}
	}