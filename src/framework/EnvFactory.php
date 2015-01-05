<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ConfigFactory.php');

/**
 * Copyright 2013 JervDesign
 * EnvFactory
 *
 * @author James Jervis
 */
class EnvFactory {

    public static function build() {

        $config = ConfigFactory::build();
        $options = $config->get('envOptions', array());
        
        Env::setDs(DIRECTORY_SEPARATOR);
        Env::setRoot($_SERVER['DOCUMENT_ROOT']);
        Env::setBase($options['dirBase']);

        if (isset($options['envVar'])) {

            Env::setAlias(getenv($options['envVar']));
        }

        Env::makeReady();

        return true;
    }

}