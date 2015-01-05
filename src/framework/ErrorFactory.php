<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ConfigFactory.php');

/**
 * Copyright 2013 JervDesign
 * ErrorFactory
 *
 * @author James Jervis
 */
class ErrorFactory {

    public static function build() {

        $config = ConfigFactory::build();
        $class = $config->get('errorClass');
        $options = $config->get('errorOptions', array());

        if (empty($class)) {
            // ERROR
            return false;
        }

        if (class_exists($class)) {

            Error::setReporter(new $class($options));

            return true;
        } else {
            //ERROR
            return false;
        }
    }

}