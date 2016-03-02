<?php
namespace Rest\Restful;

use Rest\Restful\Master\RestfulController;
use Rest\Library\Common\Common;
use Rest\Model\Entity\Users;
use Rest\Form\UsersForm;

class RestUsersController extends RestfulController{
	public function get($pId){
		$id 				= (int)$pId;
		$arrResponse 		= array(
			'success'		=> false,
			'data' 			=> array(),
			'messages'		=> array()
		);

		$model = new Users();
		$fetchRow = $model->getById($id);
		if($fetchRow){
			$arrResponse['success'] = true;
			$arrResponse['data'] 	= $fetchRow->getArrayCopy();
		}
		else{
			$arrResponse['messages'] = array(
				'message'	=> 'Não foi encontrado nenhum usuário com id "' . $id . '".',
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
			'messages'	=> array()
		);
		
		$model = new Users();
		$fetchRows = $model->getActives();
		if($fetchRows){
			$arrResponse['success'] = true;
			$arrResponse['data'] = $fetchRows;
		}
		else{
			$arrResponse['messages'] = array(
				'message'	=> 'Não foi encontrado nenhum Usuário.',
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

		$model = new Users();
		$frm = new UsersForm('user');
		$frm->setInputFilter($model->getInputFilter());
		$frm->setData($data);
		if($frm->isValid()){
			$frmData = $frm->getData();
            $frmData['ds_pass'] = md5($frmData['ds_pass']);
			$model->populate($frmData);
			if($model->getIdUser() == 0)
				$model->setIdUser(null);

			if($model->saveUser()){
				$arrResponse['success'] = true;
				$arrResponse['messages'] = array(
					'message' => 'Usuário cadastrado com sucesso',
					'code' => '1'
				);
			}
			else{
				$arrResponse['messages'] = array(
					'message' => 'Não foi possível salvar o usuário',
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

        $model = new Users();
        $fetchRow = $model->getById($pId);
        if($fetchRow){
            $data['ds_user']    = (isset($data['ds_user'])  && $fetchRow->ds_user   !== $data['ds_user'])       ? $data['ds_user']          : $fetchRow->ds_user;
            $data['ds_login']   = (isset($data['ds_login']) && $fetchRow->ds_login  !== $data['ds_login'])      ? $data['ds_login']         : $fetchRow->ds_login;
            $data['ds_pass']    = (isset($data['ds_pass'])  && $fetchRow->ds_pass   !== md5($data['ds_pass']))  ? md5($data['ds_pass'])     : $fetchRow->ds_pass;

            $frm = new UsersForm('user');
            $frm->setInputFilter($model->getInputFilter());
            $frm->setData($data);
            if($frm->isValid()){
                $arrData 			    = $frm->getData();
                $arrData['idUser']     	= $pId;
                $arrData['st_user']    	= Users::ONE;
                $arrData['dt_insert']   = $fetchRow->dt_insert;
                $arrData['dt_update']   = Common::getDateNow();

                $model->populate($arrData);
                if($model->saveUser()){
                    $arrResponse['success'] = true;
                    $arrResponse['messages'] = array(
                        'message' => 'Usuário Atualizado com sucesso.',
                        'code' => '1'
                    );
                }
                else{
                    $arrResponse['messages'] = array(
                        'message' => 'Não foi possível atualizar o usuário.',
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
		else{
			$arrResponse['messages'] = array(
				'message' => 'Usuário não localizado.',
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
		
		$model = new Users();
		$fetchRow = $model->getById($pId);
		if($fetchRow){
            if($model->deleteUser($pId) > 0){
                $arrResponse['success'] = true;
                $arrResponse['messages'] = array(
                    'message' => 'Usuário excluído com sucesso.',
                    'code' => '1'
                );
            }
            else{
                $arrResponse['messages'] = array(
                    'message' => 'Não foi possível excluir o Usuário.',
                    'code' => '2'
                );
            }

		}
		else{
			$arrResponse['messages'] = array(
				'message' => 'Usuário não localizado.',
				'code' => '2'
			);
		}
		
		$response = $this->setResponse($arrResponse);
		return $response;
	}
}