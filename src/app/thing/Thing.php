<?php
try{(Env::isReady()) ? true : die();} catch(Exception $e){die;}

class Thing {

    public $id = null;
    public $data = null;
    public $views = array();

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function setId($val) {

        $this->id = $val;
    }

    public function getId() {

        return $this->id;
    }

    public function setData($val) {

        $this->data = $val;
    }

    public function getData() {

        return $this->data;
    }

    public function setViews($val) {

        $this->views = $val;
    }

    public function getViews() {

        return $this->views;
    }

    public function setView($key, $val) {

        $this->views[$key] = $val;
    }

    public function getView($key, $def = null) {

        if (!isset($this->views[$key])) {

            return $def;
        }

        return $this->views[$key];
    }

}
