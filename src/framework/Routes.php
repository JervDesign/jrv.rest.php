<?php

/**
 * Copyright 2013 JervDesign
 * Routes
 *
 * @author James Jervis
 */
class Routes {

    public static $methodPrefix = 'action';
    private static $routesParser = null;

    public static function setParser(Routes $routesParser) {

        self::$routesParser = $routesParser;
    }

    public static function getInstance() {

        if (empty(Routes::$routesParser)) {

            $routes = new Routes();
            setParser($routes);
        }

        return Routes::$routesParser;
    }

    protected $options = array();

    public function __construct($options = array()) {

        $this->options = $options;
    }

    public function getRoute($routePath, $requestMethod = 'GET') {

        return $this->getDefaultRoute();
    }

    public function getDefaultRoute($requestMethod = 'GET') {

        return null;
    }

    public function getMethodName($requestMethod = 'GET') {

        return Routes::$methodPrefix . ucfirst(strtolower($requestMethod));
    }

}