<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ConfigFactory.php');

/**
 * Copyright 2013 JervDesign
 * DbFactory
 *
 * @author James Jervis
 */
class DbFactory {

    public static function build() {

        $config = ConfigFactory::build();
        $class = $config->get('dbClass');
        $options = $config->get('dbOptions', array());

        if (empty($class)) {
            // ERROR
            return false;
        }

        if (class_exists($class)) {

            Db::setConnection(new $class($options));

            return true;
        } else {
            //ERROR
            return false;
        }
    }
}