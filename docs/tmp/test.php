<?php
echo "<pre>";
// connect
$m = new MongoClient();

/*
var_export($m->listDBs());
$db = $m->comedy;
var_export($m->dropDB($db));
var_export($m->listDBs());
die;
 */

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

// select a database
$db = $m->comedy;

// select a collection (analogous to a relational database's table)
$collection = $db->cartoons;
$query = array("num" => array('$gt' => 123470));
$cursor = $collection->find($query);
foreach ($cursor as $document) {
    echo "-- FIND: \n";
    echo json_encode($document);
    echo "\n--\n" ;
}
/* *
$test = new Test();
var_export($test->getIterator());
$collection->insert($test->getIterator());
 /* */

/*
// add a record
$document = array( "title" => "Calvin and Hobbes", "author" => "Bill Watterson" );
$collection->insert($document);

// add another record, with a different "shape"
$document = array( "title" => "XKCD", "online" => true );
$collection->insert($document);
 *
 */

// find everything in the collection
$cursor = $collection->find();

// iterate through the results

foreach ($cursor as $document) {
    echo json_encode($document);
    echo "\n";
}