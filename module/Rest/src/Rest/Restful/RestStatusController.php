<?php
namespace Rest\Restful;

use Rest\Restful\Master\RestfulController;
use Rest\Library\Common\Common;
use Rest\Model\Entity\Feeds;
use Rest\Form\FeedsForm;

class RestStatusController extends RestfulController{
	public function get($pId){
        $arrData = explode(';', $pId);
		$id 				= (int)$arrData[0];
		$idUser 			= (int)$arrData[1];

		$arrResponse 		= array(
			'success'		=> false,
			'data' 			=> array(),
			'messages'		=> array()
		);

		$model = new Feeds();
		$fetchRow = $model->getByIdFeedAndIdUser($id, $idUser);
		if($fetchRow){
			$arrResponse['success'] = true;
			$arrResponse['data'] 	= $fetchRow->getArrayCopy();
		}
		else{
			$arrResponse['messages'] = array(
				'message'	=> 'Não foi encontrado nenhum Feed com id "' . $id . '" para este usuário.',
				'code' 		=> '2'
			);
		}

		$je = json_encode($arrResponse);
		$response = $this->getResponseWithHeader()->setContent($je);
		return $response;
	}
	 
	public function getList(){
        $params = $this->params()->fromQuery();
        $idUser = $params['id_user'];
		$arrResponse 		= array(
			'success'	=> false,
			'data' 		=> array(),
			'messages'	=> array()
		);
		
		$model = new Feeds();

		$fetchRows = $model->getByIdUser($idUser);
		if($fetchRows){
			$arrResponse['success'] = true;
			$arrResponse['data'] = $fetchRows;
		}
		else{
			$arrResponse['messages'] = array(
				'message'	=> 'Não foi encontrado nenhum feed publicado',
				'code' 		=> '2'
			);
		}
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

		$model = new Feeds();
		$frm = new FeedsForm('feed');
		$frm->setInputFilter($model->getInputFilter());
		$frm->setData($data);
		if($frm->isValid()){
			$frmData = $frm->getData();
			$model->populate($frmData);
			if($model->getIdFeed() == 0)
				$model->setIdFeed(null);

			if($model->saveFeed()){
				$arrResponse['success'] = true;
				$arrResponse['messages'] = array(
					'message' => 'Feed publicado com sucesso',
					'code' => '1'
				);
			}
			else{
				$arrResponse['messages'] = array(
					'message' => 'Não foi possível publicar seu Feed',
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
			'messages'	=> array()
		);
		
		$model = new Feeds();
		$fetchRow = $model->getById($pId);
        if($fetchRow){
			if($fetchRow->idUser !== $data['idUser']){
				$arrResponse['messages'] = array(
					'message' => 'Você não pode modificar o status de outro usuário.',
					'code' => '2'
				);
			}
			else{
				$frm = new FeedsForm('feed');
				$frm->setInputFilter($model->getInputFilter());
				$frm->setData($data);
				if($frm->isValid()){
					$arrData 			    = $frm->getData();
					$arrData['idFeed']     	= $pId;
                    $arrData['st_feed']    	= Feeds::ONE;
					$arrData['dt_insert']   = $fetchRow->dt_insert;
					$arrData['dt_update']   = Common::getDateNow();

					$model->populate($arrData);
					if($model->saveFeed()){
						$arrResponse['success'] = true;
						$arrResponse['messages'] = array(
							'message' => 'Feed Atualizado com sucesso.',
							'code' => '1'
						);
					}
					else{
						$arrResponse['messages'] = array(
							'message' => 'Não foi possível atualizar o feed.',
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
			}
        }
		else{
			$arrResponse['messages'] = array(
				'message' => 'Não encontrado feed com este ID.',
				'code' => '2'
			);
		} 
		
		$response = $this->setResponse($arrResponse);
		return $response;
	}
	 
	public function delete($pId){
		$params 			= $this->params()->fromQuery();
		$arrResponse 		= array(
			'success'	=> false,
			'data' 		=> array(),
			'messages'	=> array()
		);
		
		$model = new Feeds();
		$fetchRow = $model->getById($pId);
		if($fetchRow){
            if($model->deleteFeed($pId) > 0){
                $arrResponse['success'] = true;
                $arrResponse['messages'] = array(
                    'message' => 'Feed excluído com sucesso.',
                    'code' => '1'
                );
            }
            else{
                $arrResponse['messages'] = array(
                    'message' => 'Não foi possível excluir o seu Feed.',
                    'code' => '2'
                );
            }

		}
		else{
			$arrResponse['messages'] = array(
				'message' => 'Feed não localizado.',
				'code' => '2'
			);
		}
		
		$response = $this->setResponse($arrResponse);
		return $response;
	}
}