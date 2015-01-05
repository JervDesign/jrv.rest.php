<?php
try{(Env::isReady()) ? true : die();} catch(Exception $e){die;}

class ThingData {

    public $id = '1';
    public $status = 'active';
    public $usage = null;

}
