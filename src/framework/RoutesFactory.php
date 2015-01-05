<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ConfigFactory.php');

/**
 * Copyright 2013 JervDesign
 * RoutesFactory
 *
 * @author James Jervis
 */
class RoutesFactory {

    public static function build() {

        $config = ConfigFactory::build();
        $class = $config->get('routesClass');
        $options = $config->get('routesOptions', array());

        if (empty($class)) {
            // ERROR
            return false;
        }

        if (class_exists($class)) {

            Routes::setParser(new $class($options));

            return true;
        } else {
            //ERROR
            return false;
        }
    }

}