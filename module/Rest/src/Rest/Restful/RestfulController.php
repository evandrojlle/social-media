<?php
namespace Rest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Admin\Model\Entity\Usuario;
use Admin\Library\Common\Common;

class RestfulController extends AbstractRestfulController{
	public function get($pId){
		$model = new Usuario();
		$fetchRowUsuario = $model->getById($pId);
		
		$je = json_encode($fetchRowUsuario->getArrayCopy());
		$response = $this->getResponseWithHeader()->setContent($je);
		return $response;
	}
	 
	public function getList(){
		$response = $this->getResponseWithHeader()
		->setContent( __METHOD__ . ' - obtendo a lista de dados.');
		return $response;
	}
	 
	public function create($data){
		$response = $this->getResponseWithHeader()
		->setContent( __METHOD__ . ' Criando um novo item de dado: <b>' . $data['name'].'</b>');
		return $response;
	}
	 
	public function update($id, $data){die('TOP');
		$response = $this->getResponseWithHeader()
		->setContent(__METHOD__ . ' update current data with id =  ' . $id . ' with data of name is ' . $data['name']) ;
		return $response;
	}
	 
	public function delete($id){
		$response = $this->getResponseWithHeader()
		->setContent(__METHOD__ . ' delete current data with id =  ' . $id) ;
		return $response;
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
}