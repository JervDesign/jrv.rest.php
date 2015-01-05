<?php

/**
 * Copyright 2013 JervDesign
 * Db
 *
 * @author James Jervis
 */
class Db {

    private static $instance = null;

    public static function setInstance(Db $instance) {

        self::$instance = $instance;
    }

    public static function getInstance() {

        if (empty(self::$instance)) {

            $instance = new Db();
            self::setInstance($instance);
        }
        return self::$instance->get();
    }

    protected $options = array();

    /**
     * extend this class and override this
     * @param array $options
     */
    public function __construct($options = array()) {

        $this->options = $options;
    }

    public function get(){

        return null;
    }
}