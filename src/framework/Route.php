<?php

/**
 * Copyright 2013 JervDesign
 * Route
 *
 * @author James Jervis
 */
class Route {

    private static $schema = array(
        'id' => '',
        'path' => '',
        'classPath' => '',
        'class' => '',
        'method' => '',
        'args' => '',
    );

    public static function getInstance() {

        return Route::$schema;
    }

}