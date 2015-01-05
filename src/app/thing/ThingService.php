<?php
try{(Env::isReady()) ? true : die();} catch(Exception $e){die;}

class ThingService {

    public $thing;

    public function __construct($thing) {
        $this->thing = $thing;
    }

}
