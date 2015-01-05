<?php

class HttpIn {

    private static $inputCache = null;

    public static function buildHeaders($headers = array()) {

        foreach ($headers as $header) {

            self::setHeader($header);
        }
    }

    public static function readRequestMethod() {

        return self::readServerVar('REQUEST_METHOD');
    }

    public static function readServer($default = null) {

        if (isset($_SERVER)) {

            return $_SERVER;
        }

        return $default;
    }

    public static function readServerVar($key, $default = null) {

        $data = self::readServer(array());

        if (isset($data[$key])) {

            return $data[$key];
        }

        return $default;
    }

    public static function readGet($default = null) {

        if (!empty($_GET)) {

            return $_GET;
        }

        return $default;
    }

    public static function readGetVar($key, $default = null) {

        $data = self::readGet(array());

        if (isset($data[$key])) {

            return $data[$key];
        }

        return $default;
    }

    public static function readPost($default = null) {

        if (!empty($_POST)) {

            return $_POST;
        }

        return $default;
    }

    public static function readPostBody($default = null) {

        return self::readBody($default);
    }

    public static function readPostVar($key, $default = null) {

        $data = self::readPost(array());

        if (isset($data[$key])) {

            return $data[$key];
        }

        return $default;
    }

    public static function readPut($default = null) {

        return self::readBody($default);
    }

    public static function readDelete($default = null) {

        return self::readBody($default);
    }

    public static function readBody($default = null) {

        $data = @file_get_contents("php://input");

        if (!empty($data)) {

            self::$inputCache = $data;
            return self::$inputCache;
        }

        if (!empty(self::$inputCache)) {

            return self::$inputCache;
        }

        return $default;
    }

}

