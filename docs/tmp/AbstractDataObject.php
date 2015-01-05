<?php

class Test implements IteratorAggregate {

    public $one = 'false';
    protected $two = 'two33';
    private $three = 'three33';
    public $bool = false;
    public $num = 1234766667;

    public function toJson(){

        return json_encode($this);
    }

    public function toArray(){

        return iterator_to_array($this);
    }

    public function getIterator() {
       return new ArrayIterator(get_object_vars($this));
   }
}