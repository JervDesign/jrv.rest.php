<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Routes.php');

/**
 * Copyright 2013 JervDesign
 * RoutesMongoDb
 *
 * @author James Jervis
 */
class RoutesMongoDb extends Routes {

    public function getRoute($routePath, $requestMethod = 'GET') {

        $db = $this->getDb();

        if(empty($db) || empty($this->options['collection']) ){

            return $this->getDefaultRoute($requestMethod);
        }

        $collection = $this->options['collection'];

        $query = array('path' => (string) $routePath);

        $cursor = $db->$collection->find( $query );

        $routes = iterator_to_array($cursor);

        if(empty($routes)){

            return $this->getDefaultRoute($requestMethod);
        }

        foreach ($routes as $ind => $rt) {

            $regex = "#^{$rt['path']}\$#";

            if (preg_match($regex, $routePath, $arguments)) {

                array_shift($arguments);
                $route = $routes[$ind];

                $route['method'] = $this->getMethodName($requestMethod);
                $route['args'] = $arguments;

                return $route;
            }
        }

        return $this->getDefaultRoute($requestMethod);
    }

    public function getDefaultRoute($requestMethod = 'GET') {

        $db = $this->getDb();

        if(empty($db) || empty($this->options['collection']) ){

            return null;
        }

        $collection = $this->options['collection'];

        $query = array('default' => true);

        $cursor = $db->$collection->find( $query );

        $routes = iterator_to_array($cursor);

        if(empty($routes)){

            return null;
        }

        return $routes[0];
    }

    protected function getDb(){

        return Db::getInstance();;
    }

}