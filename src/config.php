<?php

// CONFIG
class AppConfig {

    public $envOptions = array(
        'envVar' => 'sd_env',
        'dirBase' => '.' // overridden in app
    );

    public $errorClass = 'ErrorHttp';
    public $errorOptions = null;

    public $loggerClass = 'LogFile';
    public $loggerOptions = array('id' => 'api', 'path' => 'C:\Users\jj2013\Documents\www\_logs\\');
    // array('class' => 'LogSyslog', 'options' => array('id' => 'api'))

    public $dbClass = 'DbMongoDb';
    public $dbOptions = array(
        'username' => '',
        'password' => '',
        'host' => 'localhost',
        'db' => 'app',
    );

    public $routesClass = 'RoutesArray';
    public $routesOptions = array(
        'routes' => array(
            array('path' => '/thing', 'classPath' => '/app/thing/ThingApiController.php', 'class' => 'ThingApiController'),
            array('path' => '/other/(\w+)', 'classPath' => '/app/thing/ThingApiController.php', 'class' => 'ThingApiController'),
        ),
        'defaultRoute' => array('path' => '/error', 'classPath' => '/app/error/ErrorApiController.php', 'class' => 'ErrorApiController', 'method' => 'actionGet', 'args' => null)
    );

    /*
    public $routesClass = 'RoutesMongoDb';
    public $routesOptions = array(
        'collection' => 'routes',
        'schema' => array('path' => '', 'classPath' => '', 'class' => '', 'method' => '', 'args' => array(), 'default' => false),
    );
    */
    public function get($key, $default = null){

        if(isset($this->$key)){

            return $this->$key;
        }

        return $default;
    }

}
