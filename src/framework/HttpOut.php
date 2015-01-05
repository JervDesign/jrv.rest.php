<?php

/**
 * Copyright 2013 JervDesign
 * HttpOut
 *
 * @author James Jervis
 */
class HttpOut {

    protected static $status = 200;
    protected static $data = "";

    public static function setHeader($header) {

        header($header);
    }

    public static function setContentType($contentType = 'application/json') {
        $contentType = 'Content-type: ' . $contentType;
        self::setHeader($contentType);
    }

    public static function status($status) {

        self::$status = $status;
    }

    public static function setData($data) {

        self::$data = $data;
    }

    public static function send() {

        http_response_code(self::$status);
        echo self::$data;
        die(); // end of request
    }

}