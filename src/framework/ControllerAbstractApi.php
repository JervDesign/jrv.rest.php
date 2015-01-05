<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controller.php');
/**
 * Copyright 2013 JervDesign
 * ControllerAbstractApi
 *
 * @author James Jervis
 */
abstract class ControllerAbstractApi implements Controller {

    protected $args = array();

    public function setArgs($val) {

        $this->args = $val;
    }

    public function getArgs() {

        return $this->args;
    }

    public function actionGet() {

        $response = new ResponseBasic();
        $response->code = 404;

        $this->sendResponse($response, 404);
    }

    public function actionPost() {

        $response = new ResponseBasic();
        $response->code = 404;

        $this->sendResponse($response, 404);
    }

    public function actionPut() {

        $response = new ResponseBasic();
        $response->code = 404;

        $this->sendResponse($response, 404);
    }

    public function actionDelete() {

        $response = new ResponseBasic();
        $response->code = 404;

        $this->sendResponse($response, 404);
    }

    protected function encodeResponse($response) {

        return json_encode($response);
    }

    protected function sendResponse($response = null, $status = 200) {

        HttpOut::setContentType('application/json');
        HttpOut::status($status);
        HttpOut::setData($this->encodeResponse($response));
        HttpOut::send();
        die;
    }

}