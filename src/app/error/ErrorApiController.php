<?php
try {
    (Env::isReady()) ? true : die();
} catch (Exception $e) {
    die();
}

//require_once(Env::getBase('/framework.php'));

class ErrorApiController extends ControllerAbstractApi {

    public $message = 'An unknown error has occured';
    public $code = 500;

    public function actionGet() {

        $response = new ResponseBasic();

        $response->code = $this->code;
        $response->message = $this->message;

        $this->sendResponse($response, 500);
    }

    public function actionPost() {

        $response = new ResponseBasic();

        $response->code = $this->code;
        $response->message = $this->message;

        $this->sendResponse($response, 500);
    }

    public function actionPut() {

        $response = new ResponseBasic();

        $response->code = $this->code;
        $response->message = $this->message;

        $this->sendResponse($response, 500);
    }

    public function actionDelete() {

        $response = new ResponseBasic();

        $response->code = $this->code;
        $response->message = $this->message;

        $this->sendResponse($response, 500);
    }

}
