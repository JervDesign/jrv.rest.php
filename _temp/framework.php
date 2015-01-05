<?php

// LIB
class Env {

    private static $isReady = false;
    private static $alias = '';
    private static $ds = '/';
    private static $rootDir = '';
    private static $baseDir = '';

    public static function makeReady() {

        if (!self::isReady()) {
            self::$isReady = true;
        }
    }

    public static function isReady() {

        return self::$isReady;
    }

    public static function setAlias($alias) {

        if (!self::isReady()) {
            self::$alias = $alias;
        }
    }

    public static function getAlias() {

        return self::$alias;
    }

    public static function setDs($ds) {

        if (!self::isReady()) {

            self::$ds = $ds;
        }
    }

    public static function getDs() {

        return self::$ds;
    }

    public static function setRoot($rootDir) {

        if (!self::isReady()) {

            self::$rootDir = self::cleanDirPath($rootDir);
        }
    }

    public static function getRoot($path = '') {

        return self::cleanDirPath(self::$rootDir . $path);
    }

    public static function setBase($baseDir) {

        if (!self::isReady()) {

            self::$baseDir = self::cleanDirPath($baseDir);
        }
    }

    public static function getBase($path = '') {

        return self::cleanDirPath(self::$baseDir . $path);
    }

    public static function cleanDirPath($dirPath) {

        $dirPath = str_replace('\\', self::$ds, $dirPath);
        $dirPath = str_replace('/', self::$ds, $dirPath);

        return $dirPath;
    }

}

class Log {

    private static $levels = array('EMERG' => 2, 'ALERT' => 4, 'CRIT' => 6, 'ERR' => 8, 'WARNING' => 10, 'NOTICE' => 12, 'INFO' => 14, 'DEBUG' => 16);
    private static $reportingLevel = 14; // 0 for off
    private static $logger = null;

    public static function setReportingLevel($level = 'INFO') {

        if (isset(self::$levels[$level])) {
            self::$reportingLevel = self::$levels[$level];
        }
    }

    public static function setLogger(Log $logger) {

        self::$logger = $logger;
    }

    public static function getInstance() {

        if (empty(self::$logger)) {

            $log = new Log();
            self::setLogger($log);
        }
        return self::$logger;
    }

    /**
     *
     * @param mixed $message
     * @param int $code
     * @param string $level
     * @return void
     */
    public static function write($message = '', $code = 500, $level = 'INFO', $method = '', $file = '', $line = '') {

        if (!isset(self::$levels[$level])) {
            // not valid level
            return;
        }
        if (self::$levels[$level] > self::$reportingLevel) {
            // out of reporting range
            return;
        }

        $u = substr((string) microtime(), 1, 8);

        $data = array(
            'timestamp' => gmdate("Y-m-d H:i:s{$u} O"),
            'level' => $level,
            'code' => $code,
            'message' => $message
        );

        if (!empty($method)) {
            $data['method'] = $method;
        }
        if (!empty($file)) {
            $data['file'] = $file;
        }
        if (!empty($line)) {
            $data['line'] = $line;
        }

        $logger = self::getInstance();

        $logger->writeLog($data);
    }

    protected $options = array();

    /**
     * extend this class and override this
     * @param array $options
     */
    public function __construct($options = array()) {

        $this->options = $options;
    }

    /**
     * extend this class and override this
     * @param array $data
     */
    public function writeLog($data) {
        // default does nothing
    }

}

class Errors {

    private static $errorReporter = null;
    public static $errors = array();

    public static function setReporter(Errors $errorReporter) {

        self::$errorReporter = $errorReporter;
    }

    public static function getInstance() {

        if (empty(Errors::$errorReporter)) {

            $errors = new Errors();
            Errors::setReporter($errors);
        }
        return Errors::$errorReporter;
    }

    public static function clear() {

        self::$errors = array();
    }

    public static function get() {

        return self::$errors;
    }

    public static function set($message, $code = 500) {

        self::$errors[] = array('message' => $message, 'code' => $code);
    }

    public static function thrw($backtrace = false) {

        if (empty(self::$errors)) {
            // nothing to throw
            return;
        }

        $reporter = self::getInstance();

        $ret = $reporter->throwErrors($backtrace);
        self::clear();

        return $ret;
    }

    public static function throwNew($message, $code = 500, $backtrace = false) {

        self::set($message, $code);

        return self::thrw($backtrace);
    }

    protected $options = array();

    /**
     * extend this class and override this
     * @param array $options
     */
    public function __construct($options = array()) {

        $this->options = $options;
    }

    /**
     * extend this class and override this
     * @param array $data
     */
    public function throwErrors($backtrace) {

        $code = Errors::$errors[0]['code'];
        $message = Errors::$errors[0]['code'];

        if ($backtrace) {

            $message = $message . ' [BACKTRACE]: ' . json_encode(Errors::$errors);
        }

        throw new Exception($message, $code);
    }

}

class Db {

    private static $instance = null;

    public static function setInstance(Db $instance) {

        self::$instance = $instance;
    }

    public static function getInstance() {

        if (empty(self::$instance)) {

            $instance = new Db();
            self::setInstance($instance);
        }
        return self::$instance->get();
    }

    protected $options = array();

    /**
     * extend this class and override this
     * @param array $options
     */
    public function __construct($options = array()) {

        $this->options = $options;
    }

    public function get(){

        return null;
    }
}

class HttpInbound {

    public function getRequestVars() {

        return $_REQUEST;
    }

    public function getRequestVar($key, $def = null) {

        if (!isset($_REQUEST[$key])) {

            return $def;
        }

        return $_REQUEST[$key];
    }

    public function getPostVars() {

        return $_POST;
    }

    public function getPostVar($key, $def = null) {

        if (!isset($_POST[$key])) {

            return $def;
        }

        return $_POST[$key];
    }

    public function getBodyVar() {

        return http_get_request_body();
    }

    public function getBodyJsonVars($def = null) {

        $body = $this->getBodyVar();
        $json = json_decode($body);
        if (!$json) {

            return $def;
        }

        return $json;
    }

    public function getServerVar($key, $default = null) {

        if (isset($_SERVER[$key])) {

            return $_SERVER[$key];
        }

        return $default;
    }

}

interface Orm {}

interface Auth {

}

interface Acl {

}

interface User {

}

interface I18n {}

interface Controller {

    public function setArgs($val);

    public function getArgs();

    public function actionGet();

    public function actionPost();

    public function actionPut();

    public function actionDelete();
}

// CORE
class BasicResponse {

    public $code = 0;
    public $message = '';

}

class GenericResponse extends BasicResponse {

    public $data = null;

}

class ErrorHttp extends Errors {

    public function throwErrors($backtrace) {

        if ($backtrace) {
            $response = new BasicResponse();

            $response->code = 500;
            $response->message = Errors::$errors;
        } else {

            $code = Errors::$errors[0]['code'];
            $message = Errors::$errors[0]['message'];

            $response = new BasicResponse();

            $response->code = $code;
            $response->message = $message;
        }

        HttpResponse::setContentType('application/json');
        HttpResponse::status(500);
        HttpResponse::setData(json_encode($response));
        HttpResponse::send();
        die;
    }

}

class LogFile extends Log {

    public function writeLog($data) {

        try {
            $message = "\n" . json_encode($data);
            @error_log($message, 3, $this->options['path'] . gmdate('Y-m-d') . '_' . $this->options['id'] . '.log');
        } catch (Exception $e) {
            // do nothing on error
        }
    }

}

class LogSyslog extends Log {

    public function writeLog($data) {

        try {

            $priority = constant('LOG_' . $data['level']);

            @openlog($this->options['id'], 0, LOG_SYSLOG);

            $message = json_encode($data);

            /*
             * Record or die (die - because logging problem has to be solved first)
             */
            if (!@syslog($priority, $message)) {
                /*
                 * no need to tell the world know that your logging isn't working, just
                 * use some error code that only your developers are aware of.
                 */
                // @todo die('2458 error.');
            }

            @closelog();
        } catch (Exception $e) {
            // do nothing on error
        }
    }

}

class Route {

    private static $schema = array(
        'id' => '',
        'path' => '',
        'classPath' => '',
        'class' => '',
        'method' => '',
        'args' => '',
    );

    public static function getInstance() {

        return Route::$schema;
    }

}

class Routes {

    public static $methodPrefix = 'action';
    private static $routesParser = null;

    public static function setParser(Routes $routesParser) {

        self::$routesParser = $routesParser;
    }

    public static function getInstance() {

        if (empty(Routes::$routesParser)) {

            $routes = new Routes();
            setParser($routes);
        }

        return Routes::$routesParser;
    }

    protected $options = array();

    public function __construct($options = array()) {

        $this->options = $options;
    }

    public function getRoute($routePath, $requestMethod = 'GET') {

        return $this->getDefaultRoute();
    }

    public function getDefaultRoute($requestMethod = 'GET') {

        return null;
    }

    public function getMethodName($requestMethod = 'GET') {

        return Routes::$methodPrefix . ucfirst(strtolower($requestMethod));
    }

}

class RoutesArray extends Routes {

    public function getRoute($routePath, $requestMethod = 'GET') {

        if (!isset($this->options['routes'])) {

            return $this->getDefaultRoute($requestMethod);
        }

        $routes = $this->options['routes'];

        foreach ($routes as $ind => $rt) {

            $regex = "#^{$rt['path']}\$#";

            if (preg_match($regex, $routePath, $arguments)) {

                array_shift($arguments);
                $route = $routes[$ind];

                $route['method'] = $this->getMethodName($requestMethod);
                $route['args'] = $arguments;

                return $route;
            }
        }

        return $this->getDefaultRoute($requestMethod);
    }

    public function getDefaultRoute($requestMethod = 'GET') {

        if (isset($this->options['defaultRoute'])) {

            return $this->options['defaultRoute'];
        }

        return null;
    }

}

class RoutesMongoDb extends Routes {

    public function getRoute($routePath, $requestMethod = 'GET') {

        $db = $this->getDb();

        if(empty($db) || empty($this->options['collection']) ){

            return $this->getDefaultRoute($requestMethod);
        }

        $collection = $this->options['collection'];

        $query = array('path' => (string) $routePath);

        $cursor = $db->$collection->find( $query );

        $routes = iterator_to_array($cursor);

        if(empty($routes)){

            return $this->getDefaultRoute($requestMethod);
        }

        foreach ($routes as $ind => $rt) {

            $regex = "#^{$rt['path']}\$#";

            if (preg_match($regex, $routePath, $arguments)) {

                array_shift($arguments);
                $route = $routes[$ind];

                $route['method'] = $this->getMethodName($requestMethod);
                $route['args'] = $arguments;

                return $route;
            }
        }

        return $this->getDefaultRoute($requestMethod);
    }

    public function getDefaultRoute($requestMethod = 'GET') {

        $db = $this->getDb();

        if(empty($db) || empty($this->options['collection']) ){

            return null;
        }

        $collection = $this->options['collection'];

        $query = array('default' => true);

        $cursor = $db->$collection->find( $query );

        $routes = iterator_to_array($cursor);

        if(empty($routes)){

            return null;
        }

        return $routes[0];
    }

    protected function getDb(){

        return Db::getInstance();;
    }

}

abstract class AbstractApiController implements Controller {

    protected $args = array();

    public function setArgs($val) {

        $this->args = $val;
    }

    public function getArgs() {

        return $this->args;
    }

    public function actionGet() {

        $response = new BasicResponse();
        $response->code = 404;

        $this->sendResponse($response, 404);
    }

    public function actionPost() {

        $response = new BasicResponse();
        $response->code = 404;

        $this->sendResponse($response, 404);
    }

    public function actionPut() {

        $response = new BasicResponse();
        $response->code = 404;

        $this->sendResponse($response, 404);
    }

    public function actionDelete() {

        $response = new BasicResponse();
        $response->code = 404;

        $this->sendResponse($response, 404);
    }

    protected function encodeResponse($response) {

        return json_encode($response);
    }

    protected function sendResponse($response = null, $status = 200) {

        HttpResponse::setContentType('application/json');
        HttpResponse::status($status);
        HttpResponse::setData($this->encodeResponse($response));
        HttpResponse::send();
        die;
    }

}

class HttpUtils {

    public static function setHeadersNoCache() {

        HttpResponse::setHeader('Access-Control-Allow-Origin', '*');
        HttpResponse::setHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
        HttpResponse::setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
        HttpResponse::setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        HttpResponse::setHeader('Cache-Control', 'post-check=0, pre-check=0', false);
        HttpResponse::setHeader('Pragma', 'no-cache');
    }

    public static function setJsonContentType() {

        HttpResponse::setContentType('application/json');
    }

    public static function sendJsonResponse($status, $data) {
        HttpResponse::setContentType('application/json');
        // set headers (should be up to controller)
        HttpResponse::status($status);
        //
        HttpResponse::setData($data);
        HttpResponse::send();
    }

}
