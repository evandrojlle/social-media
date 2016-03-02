<?php
namespace Rest\Controller;

use Rest\Controller\Master\ClientController;
use Zend\View\Model\JsonModel;

class SocialMediaController extends ClientController{
	public function indexAction(){
        try{
            $params 				=  $this->getEvent()->getRouteMatch()->getParams();
            $query 					= $this->params()->fromQuery();
            $query['method'] 		= $params['method'];

            if($params['method'] === 'create' || $params['method'] == 'update'){
                $request = $this->getRequest();
                if($request->isPost()){
                    $query['ds_feed'] = $request->getPost('post');
                    if($params['method'] == 'update')
                        $query['id'] = $request->getPost('id');
                }
                else
                    $query['ds_feed'] = $query['post'];

                if(isset($query['post']))
                    unset($query['post']);
            }

            if($params['method'] == 'get-list')
                $route = 'rest-sm?' . http_build_query($query);
            else
                $route = 'rest-sm';

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