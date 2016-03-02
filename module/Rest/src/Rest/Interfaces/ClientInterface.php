<?php
/**
 * Created by PhpStorm.
 * User: evandro.oliveira
 * Date: 10/02/2016
 * Time: 15:56
 */

namespace Rest\Interfaces;


interface ClientInterface{
    public function getIp();

    public function getOs();

    public function mobileDetect();

    public function getHashDevice();

    public function restClient($pData, $pRoute);

    public function getResponseWithHeader();

    public function getAuthService();

    public function getSessionStorage();

    public function getStorage();

    public function setEntityManager(\Zend\Db\Adapter\Adapter $em);

    public function getEntityManager();

    public function getRelated($id, $class, $method, $column, $em);

    public function getConfig();

    public function getNameRoutes();

    public function getNameRoutesByString($string);
}