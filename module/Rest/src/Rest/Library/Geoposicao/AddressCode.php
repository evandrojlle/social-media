<?php
	namespace Rest\Library\Geoposicao;
	
	use Rest\Library\Common\Common;
	use Zend\Http\Client;
	use Zend\Http\Client\Adapter\Exception\RuntimeException as ClientRuntimeException;
	
	class AddressCode{
		protected $_url = "http://cep.republicavirtual.com.br/web_cep.php?cep=%s&formato=json";
		
		protected $_client;
		
		protected $_result;
		
		protected $_json;
		
		protected $_number;
		
		public function __construct($pAddressCode){
			Common::allowUrlFopen(true);
			$addressCode 	= preg_replace(array('/\./', '/-/'), '', $pAddressCode);	
			$url 			= sprintf($this->_url, $addressCode);
			$this->_json 	= @file_get_contents($url);
			$this->_result	= json_decode($this->_json);
			Common::allowUrlFopen(false);
		}
		
		public function streamSocketClient($pAddressCode){
			Common::allowUrlFopen(true);
			$addressCode 	= preg_replace(array('/\./', '/-/'), '', $pAddressCode);	
			$url 			= sprintf($this->_url, urlencode($addressCode));
			$this->_client	= new Client($url);
			$this->_json 	= json_decode($this->_client->send()->getBody());
		}
		
		protected function getResult(){
			return $this->_result;
		}
		
		public function getStatus(){
			if(isset($this->_result->resultado))
				return $this->_result->resultado;
			return false;
		}
		
		public function getStatusTxt(){
			if(isset($this->_result->resultado_txt))
				return $this->_result->resultado_txt;
			return false;
		}
		
		public function getUf(){
			if(isset($this->_result->uf))
				return $this->_result->uf;
			return false;
		}
		
		public function getCity(){
			if(isset($this->_result->cidade))
				return $this->_result->cidade;
			return false;
		}
		
		public function getDistrict(){
			if(isset($this->_result->bairro))
				return $this->_result->bairro;
			return false;
		}
		
		public function getStreetType(){
			if(isset($this->_result->tipo_logradouro))
				return $this->_result->tipo_logradouro;
			return false;
		}
		
		public function getStreet(){
			if(isset($this->_result->logradouro))
				return $this->_result->logradouro;
			return false;
		}
		
		public function getNumber(){
			if(null != $this->_number)
				return $this->_number;
		}
		
		public function setNumber($pNumber){
			$this->_number = $pNumber;
		}
		
		public function getAddress(){
			$result 		= $this->getResult();
			if((int)$result->resultado === 1){
				$streetType	= $result->tipo_logradouro . ' ';
				$street 	= $result->logradouro . ', ';
				$number 	= (!$this->getNumber()) ? null : $this->getNumber() . ', ';
				$district 	= $result->bairro . ', ';
				$city		= $result->cidade . ', ';
				$state		= $result->uf;
				
				return $streetType . $street . $number . $district . $city . $state;
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
		
		public function getJson(){
			return $this->_json;
		}
	}