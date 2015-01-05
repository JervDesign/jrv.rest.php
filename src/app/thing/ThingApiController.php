<?php
try{(Env::isReady()) ? true : die();} catch(Exception $e){die;}

//require_once(Env::getBase('/framework.php'));

class ThingApiController extends ControllerAbstractApi {

    public function actionGet() {

        $response = new ResponseBasic();
        $response->code = 200;
        $response->message = 'Thing GET, args: ' . var_export($this->getArgs(), true);

        $this->sendResponse($response, 200);
    }

    public function actionPost() {

        $response = new ResponseBasic();
        $response->code = 200;
        $response->message = 'Thing POST';

        $this->sendResponse($response, 200);
    }

    public function actionPut() {

        $response = new ResponseBasic();
        $response->code = 200;
        $response->message = 'Thing PUT';

        $this->sendResponse($response, 200);
    }

    public function actionDelete() {

        $response = new ResponseBasic();
        $response->code = 200;
        $response->message = 'Thing DELETE';

        $this->sendResponse($response, 200);
    }
}
