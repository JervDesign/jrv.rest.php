<?php

/**
 * Copyright 2013 JervDesign
 * Error
 *
 * @author James Jervis
 */
class Error {

    private static $errorReporter = null;
    public static $errors = array();

    public static function setReporter(Error $errorReporter) {

        self::$errorReporter = $errorReporter;
    }

    public static function getInstance() {

        if (empty(Error::$errorReporter)) {

            $errors = new Error();
            Error::setReporter($errors);
        }
        return Error::$errorReporter;
    }

    public static function clear() {

        self::$errors = array();
    }

    public static function get() {

        return self::$errors;
    }

    public static function set($message, $code = 500) {

        self::$errors[] = array('message' => $message, 'code' => $code);
    }

    public static function thrw($backtrace = false) {

        if (empty(self::$errors)) {
            // nothing to throw
            return;
        }

        $reporter = self::getInstance();

        $ret = $reporter->throwErrors($backtrace);
        self::clear();

        return $ret;
    }

    public static function throwNew($message, $code = 500, $backtrace = false) {

        self::set($message, $code);

        return self::thrw($backtrace);
    }

    protected $options = array();

    /**
     * extend this class and override this
     * @param array $options
     */
    public function __construct($options = array()) {

        $this->options = $options;
    }

    /**
     * extend this class and override this
     * @param array $data
     */
    public function throwErrors($backtrace) {

        $code = Error::$errors[0]['code'];
        $message = Error::$errors[0]['code'];

        if ($backtrace) {

            $message = $message . ' [BACKTRACE]: ' . json_encode(Error::$errors);
        }

        throw new Exception($message, $code);
    }

}