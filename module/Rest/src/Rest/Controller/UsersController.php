<?php
namespace Rest\Controller;

use Rest\Controller\Master\ClientController;
use Zend\View\Model\JsonModel;

class UsersController extends ClientController{
	public function indexAction(){
        try{
            $params 				=  $this->getEvent()->getRouteMatch()->getParams();
            $query 					= $this->params()->fromQuery();
            $query['method'] 		= $params['method'];

            if($params['method'] === 'create' || $params['method'] == 'update'){
                $request = $this->getRequest();
                if($request->isPost()){
                    $query['ds_user']   = $request->getPost('name');
                    $query['ds_login']  = $request->getPost('login');
                    $query['ds_pass']  = $request->getPost('pass');
                    if($params['method'] == 'update')
                        $query['id'] = $request->getPost('id');
                }
                else{
                    $query['ds_user']   = $query['name'];
                    $query['ds_login']  = $query['login'];
                    $query['ds_pass']   = $query['pass'];
                }

                if(isset($query['name']))
                    unset($query['name']);

                if(isset($query['login']))
                    unset($query['login']);

                if(isset($query['pass']))
                    unset($query['pass']);
            }

            if($params['method'] == 'get-list')
                $route = 'rest-usr?' . http_build_query($query);
            else
                $route = 'rest-usr';

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