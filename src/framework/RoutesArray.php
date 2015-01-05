<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Routes.php');

/**
 * Copyright 2013 JervDesign
 * RoutesArray
 *
 * @author James Jervis
 */
class RoutesArray extends Routes {

    public function getRoute($routePath, $requestMethod = 'GET') {

        if (!isset($this->options['routes'])) {

            return $this->getDefaultRoute($requestMethod);
        }

        $routes = $this->options['routes'];

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
        
        // @todo this introduces a dependency on Error that might be avoided
        Error::throwNew('Route does not exist.', 404);
        //return $this->getDefaultRoute($requestMethod);
    }

    public function getDefaultRoute($requestMethod = 'GET') {

        if (isset($this->options['defaultRoute'])) {

            return $this->options['defaultRoute'];
        }

        return null;
    }

}