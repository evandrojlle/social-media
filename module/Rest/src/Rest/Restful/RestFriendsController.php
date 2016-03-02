<?php
namespace Rest\Restful;

use Rest\Restful\Master\RestfulController;
use Rest\Library\Common\Common;
use Rest\Model\Entity\Friends;
use Rest\Form\FriendsForm;

class RestFriendsController extends RestfulController{
	public function get($pId){
		$id 				= (int)$pId;
		$arrResponse 		= array(
			'success'		=> false,
			'data' 			=> array(),
			'messages'		=> array()
		);

		$model = new Friends();
		$fetchRow = $model->getByIdUser($id);
		if($fetchRow){
			$arrResponse['success'] = true;
			$arrResponse['data'] 	= $fetchRow;
		}
		else{
			$arrResponse['messages'] = array(
				'message'	=> 'Não foi encontrado nenhum Amigo com id "' . $id . '".',
				'code' 		=> '2'
			);
		}

		$je = json_encode($arrResponse);
		$response = $this->getResponseWithHeader()->setContent($je);
		return $response;
	}
	 
	public function getList(){
		$arrResponse 		= array(
			'success'	=> false,
			'data' 		=> array(),
            'messages'	=> array(
                'message' => __METHOD__ . ' não implementado.',
                'code' => '1'
            )
		);

		$je = json_encode($arrResponse);		
		$response = $this->getResponseWithHeader()->setContent($je);
		return $response;
	}
	 
	public function create($pData){
		$data 				= Common::renameIdFieldInArray($pData);
		$arrResponse 		= array(
			'success'	=> false,
			'messages'	=> array()
		);

		$model = new Friends();
		$frm = new FriendsForm('friend');
		$frm->setInputFilter($model->getInputFilter());
		$frm->setData($data);
		if($frm->isValid()){
			$frmData = $frm->getData();
			$model->populate($frmData);
            $insert = $model->saveFriend();
			if($insert === 1){
				$arrResponse['success'] = true;
				$arrResponse['messages'] = array(
					'message' => 'Amizade salva com sucesso',
					'code' => '1'
				);
			}
			elseif($insert === 2){
				$arrResponse['success'] = true;
				$arrResponse['messages'] = array(
					'message' => 'Você já é amigo desse usuário',
					'code' => '1'
				);
			}
			else{
				$arrResponse['messages'] = array(
					'message' => 'Não foi possível salvar a amizade',
					'code' => '2'
				);
			}
		}
		else{
			$frm->hasValidated();
			foreach($frm->getMessages() as $message){
				if(count($message) > 1){
					$arrMessages['messages'] = array();
					foreach ($message as $msg){
						$arrMessages['messages'][] = array(
							'message' => $msg,
							'code' => '4'
						);
					}
					$arrResponse = $arrMessages;
				}
				else{
					$arrResponse['messages'] = array(
						'message' => $message,
						'code' => '4'
					);
				}
			}
		}


		$response = $this->setResponse($arrResponse);
		return $response;
	}
	 
	public function update($pId, $pData){
        $data 				= Common::renameIdFieldInArray($pData);
        $arrResponse 		= array(
			'success'	=> false,
			'data' 		=> array(),
			'messages'	=> array(
                'message' => __METHOD__ . ' não implementado.',
                'code' => '1'
            )
		);

		$response = $this->setResponse($arrResponse);
		return $response;
	}
	 
	public function delete($pId){
		$params 			= $this->params()->fromQuery();
		$arrResponse 		= array(
			'success'	=> false,
			'data' 		=> array(),
            'messages'	=> array(
                'message' => __METHOD__ . ' não implementado.',
                'code' => '1'
            )
		);

		$response = $this->setResponse($arrResponse);
		return $response;
	}
}