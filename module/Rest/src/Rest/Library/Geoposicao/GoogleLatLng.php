<?php
	namespace Admin\Library\Geoposicao;
	
	use Admin\Library\Common\Common;
	use Zend\Http\Client;
	use Zend\Http\Client\Adapter\Exception\RuntimeException as ClientRuntimeException;
	
	class GoogleLatLng{
		protected $_url = 'http://maps.google.com/maps/api/geocode/json?latlng=%s&sensor=false';
		protected $_client;
		protected $_json;
		protected $_object;
		protected $_result;
		
		public function __construct($pLatLng){
			try {
				Common::allowUrlFopen(true);
            	$url = sprintf($this->_url, urlencode($pLatLng));
            	$this->_client = new Client($url);
            	$this->_json = $this->_client->send()->getBody();
            	$this->_object = json_decode($this->_json);
            	$this->_result = (false == $this->_object) ? array('results' => array(), 'status' => 'JSON_ERROR') : $this->_object;
            	Common::allowUrlFopen(false);
        	} 
        	catch (ClientRuntimeException $e) {
            	$this->_result = array('results' => array(), 'status' => 'HTTP_CLIENT_ERROR');
       	 	} 
        	catch (\Exception $e) {
            	$this->_result = array('results' => array(), 'status' => 'UNKNOWN_ERROR');
        	}
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
		
		protected function getObject(){
			return $this->_object;
		}
		
		protected function getResult(){
			return $this->_result;
		}
		
		public function getAddressComponents(){
			$results = $this->_result->results;
			$addressComponents = array();
			foreach ($results as $k => $value){
				$addressComponents[] = $results[$k]->address_components;
			}
						
			return $addressComponents;
		}
		
		//\Admin\Library\Common\Common::debugger($result);
		public function getPostalCode(){
			foreach($this->_result->results as $result){
				if(isset($result->types) && $result->types[0] == 'postal_code'){
					//return $result->address_components[0]->long_name;					
				}
			}
			return false;
		}
		
		// ENDEREÇO COMPLETO
		public function getFormattedAddress(){
			foreach($this->_result->results as $results)
				if(isset($results->types) && $results->types[0] == 'street_address')
					return $results->formatted_address;
			
			return false;
		}
		
		// LOGRADOURO
		public function getAddress(){
			foreach($this->_result->results as $results){
				if(isset($results->types) && $results->types[0] == 'street_address'){
					return $results->address_components[1]->long_name;
				}
			}
			return false;
		}
		
		public function getTypeAddress(){
			$address = $this->getAddress();
			if($address){
				$typeAddress = substr($address, 0, strpos($address, ' '));
				return $typeAddress;
			}
			return false;
		}
		
		public function getStreet(){
			$address = $this->getAddress();
			if($address){
				$street = substr($address, strpos($address, ' '));
				return $street;
			}
			return false;
		}
		
		// BAIRRO
		public function getNeighborhood(){
			foreach($this->_result->results as $results){
				if(isset($results->types) && $results->types[0] == 'street_address'){
					if($results->address_components[2]->types[0] == 'neighborhood'){
						return $results->address_components[2]->long_name;
					}
				}
			}
			return false;
		}
		
		public function getCidade(){
			foreach($this->_result->results as $results){
				if(isset($results->types) && $results->types[0] == 'street_address'){
					if($results->address_components[2]->types[0] != 'neighborhood'){
						return $results->address_components[2]->long_name;
					} 
					else{
						return $results->address_components[3]->long_name;
					}
				}
			}	
			return false;
		}
		
		public function getNomeUf(){
			foreach($this->_result->results as $results){
				if(isset($results->types) && $results->types[0] == 'street_address'){
					if($results->address_components[2]->types[0] != 'neighborhood'){
						return substr($results->address_components[4]->long_name, 9);
					}
					else{
						return substr($results->address_components[5]->long_name, 9);
					}
				}
			}
			return false;
		}
		
		public function getSiglaUf(){
			foreach($this->_result->results as $results){
				if(isset($results->types) && $results->types[0] == 'street_address'){
					if($results->address_components[2]->types[0] != 'neighborhood'){
						return $results->address_components[4]->short_name;
					}
					else{
						return $results->address_components[5]->short_name;
					}
				}
			}
			return false;
		}
		
		public function getPais(){
			foreach($this->_result->results as $results){
				if(isset($results->types) && $results->types[0] == 'street_address'){
					if($results->address_components[2]->types[0] != 'neighborhood'){
						return $results->address_components[5]->long_name;
					}
					else{
						return $results->address_components[6]->long_name;
					}
				}
			}
			return false;
		} 
		
		//\Admin\Library\Common\Common::debugger($results);
		public function getCodePrefix(){
			foreach($this->_result->results as $results){
				if(isset($results->types) && $results->types[0] == 'street_address'){
					if($results->address_components[2]->types[0] != 'neighborhood'){
						return $results->address_components[6]->long_name;
					}
					else{
						return $results->address_components[7]->long_name;
					}
				}
			}
			return false;
		} 
		
		public function getGeometry(){
			foreach($this->_result->results as $results){
				if(isset($results->types) && $results->types[0] == 'street_address'){
					return $results->geometry;
				}
				
			}
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
	}