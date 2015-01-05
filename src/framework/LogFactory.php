<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ConfigFactory.php');

/**
 * Copyright 2013 JervDesign
 * LogFactory
 *
 * @author James Jervis
 */
class LogFactory {

    public static function build() {

        $config = ConfigFactory::build();
        $class = $config->get('logClass');
        $options = $config->get('logOptions', array());

        if (empty($class)) {
            // ERROR
            return false;
        }

        if (class_exists($class)) {

            Log::setLogger(new $class($options));

            return true;
        } else {
            //ERROR
            return false;
        }
    }

}