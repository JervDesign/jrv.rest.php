<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Error.php');

/**
 * Copyright 2013 JervDesign
 * ErrorHttp
 *
 * @author James Jervis
 */
class ErrorHttp extends Error {

    public function throwErrors($backtrace) {

        if ($backtrace) {
            $response = new ResponseBasic();

            $response->code = 500;
            $response->message = Error::$errors;
        } else {

            $code = Error::$errors[0]['code'];
            $message = Error::$errors[0]['message'];

            $response = new ResponseBasic();

            $response->code = $code;
            $response->message = $message;
        }

        HttpOut::setContentType('application/json');
        HttpOut::status(500);
        HttpOut::setData(json_encode($response));
        HttpOut::send();
        die();
    }

}