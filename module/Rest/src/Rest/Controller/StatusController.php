<?php
namespace Rest\Controller;

use Rest\Controller\Master\ClientController;
use Zend\View\Model\JsonModel;

class StatusController extends ClientController{
	public function indexAction(){
        try{
            $params 				=  $this->getEvent()->getRouteMatch()->getParams();
            $query 					= $this->params()->fromQuery();
            $query['method'] 		= $params['method'];

            if($params['method'] === 'create' || $params['method'] == 'update'){
                $request = $this->getRequest();
                if($request->isPost()){
                    if($params['method'] == 'update')
                        $query['id'] = $request->getPost('id');

                    $query['ds_feed'] = $request->getPost('post');
                    $query['idUser']  = $request->getPost('id_user');

                }
                else{
                    $query['ds_feed']   = $query['post'];
                    $query['idUser']    = $query['id_user'];
                }

                if(isset($query['post']))
                    unset($query['post']);

                if(isset($query['id_user']))
                    unset($query['id_user']);
            }

            if($params['method'] == 'get-list')
                $route = 'rest-stts?' . http_build_query($query);
            else
                $route = 'rest-stts';

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