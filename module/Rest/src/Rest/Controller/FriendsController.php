<?php
namespace Rest\Controller;

use Rest\Controller\Master\ClientController;
use Zend\View\Model\JsonModel;

class FriendsController extends ClientController{
	public function indexAction(){
        try{
            $params 				=  $this->getEvent()->getRouteMatch()->getParams();
            $query 					= $this->params()->fromQuery();
            $query['method'] 		= $params['method'];

            if($params['method'] === 'create' || $params['method'] == 'update'){
                $request = $this->getRequest();
                if($request->isPost()){
                    $query['id_user']   = $request->getPost('id');
                    $query['id_friend']  = $request->getPost('id_frnd');
                }
                else{
                    $query['id_user']   = $query['id'];
                    $query['id_friend'] = $query['id_frnd'];
                }

                if(isset($query['id']))
                    unset($query['id']);

                if(isset($query['id_frnd']))
                    unset($query['id_frnd']);
            }

            if($params['method'] == 'get-list')
                $route = 'rest-frnd?' . http_build_query($query);
            else
                $route = 'rest-frnd';

            $response = $this->restClient($query, $route);
            $body = $response->getContent();
            $o = json_decode($body);

            if(json_last_error() == 0){
                $view = new JsonModel((array)$o);
                $view->setTerminal(true);
                return $view;
            }
            return $response;
        }
        catch (\Exception $e){
            $response = $this->getResponse();
            $arrMessage = array('message' => $e->getMessage(), 'code' => '4');
            $response->setContent(json_encode($arrMessage));
        }

        $view = new JsonModel($arrMessage);
        $view->setTerminal(true);
        return $view;
	}
}