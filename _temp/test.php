<?php

// LIB

class Env {

    const PROD_ALIAS = 'prod';
    const QA_ALIAS = 'qa';

    private static $isReady = false;
    private static $alias = 'qa';
    private static $ds = '/';
    private static $rootDir = '';
    private static $baseDir = '';

    public function makeReady() {

        self::$isReady = true;
    }

    public function isReady() {

        return self::$isReady;
    }

    public function setAlias($alias) {

        if (!self::$isReady) {
            self::$alias = $alias;
        }
    }

    public function getAlias() {

        return self::$alias;
    }

    public function setDs($ds) {

        if (!self::$isReady) {

            self::$ds = $ds;
        }
    }

    public function getDs() {

        return self::$ds;
    }

    public function setRoot($rootDir) {

        if (!self::$isReady) {

            self::$rootDir = $this->cleanDirPath($rootDir);
        }
    }

    public function getRoot($path = '') {

        return $this->cleanDirPath(self::$rootDir . $path);
    }

    public function setBase($baseDir) {

        if (!self::$isReady) {

            self::$baseDir = $this->cleanDirPath($baseDir);
        }
    }

    public function getBase($path = '') {

        return $this->cleanDirPath(self::$baseDir . $path);
    }

    public function cleanDirPath($dirPath) {

        $dirPath = str_replace('\\', self::$ds, $dirPath);
        $dirPath = str_replace('/', self::$ds, $dirPath);

        return $dirPath;
    }

}

class Log {

    private static $levels = array('EMERG' => 2, 'ALERT' => 4, 'CRIT' => 6, 'ERR' => 8, 'WARNING' => 10, 'NOTICE' => 12, 'INFO' => 14, 'DEBUG' => 16);
    public static $reportingLevel = 14; // 0 for off

    public static function setReportingLevel($level = 'INFO') {

        if (isset(self::$levels[$level])) {
            self::$reportingLevel = self::$levels[$level];
        }
    }

    public $path = "/var/log/cpanel/";
    public $id = "cpanel";
    public $methods = array('writeFile', 'writeSyslog');

    /**
     *
     * @param mixed $message
     * @param int $code
     * @param string $level
     * @return void
     */
    public function write($message = '', $code = 500, $level = 'INFO', $method = '', $file = '', $line = '') {

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

        foreach ($this->methods as $methodName) {

            if (method_exists($this, $methodName)) {
                $this->$methodName($data);
            }
        }
    }

    public function writeFile($data) {

        try {
            $message = "\n" . json_encode($data);
            @error_log($message, 3, $this->path . gmdate('Y-m-d') . '_' . $this->id . '.log');
        } catch (Exception $e) {
            // do nothing on error
        }
    }

    public function writeSyslog($data) {

        try {

            $priority = constant('LOG_' . $data['level']);

            @openlog($this->id, 0, LOG_SYSLOG);

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

class HttpInbound {

    protected static $routeArgs = array();

    public function setRouteArgs($val) {

        self::$routeArgs = $val;
    }

    public function getRouteArgs() {

        return self::$routeArgs;
    }

    public function setRouteArg($key, $val) {

        self::$routeArgs[$key] = $val;
    }

    public function getRouteArg($key, $def = null) {

        if (!isset(self::$routeArgs[$key])) {

            return $def;
        }

        return self::$routeArgs[$key];
    }

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

class GenericResponse {

    public $code = 0;
    public $message = '';
    public $data = null;

}

class ApiRouter {

    const ROUTE_KEY = '__route__';
    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';
    const HTTP_PUT = 'PUT';
    const HTTP_DELETE = 'DELETE';
    const KEY_ROUTE = 'route';
    const KEY_FILEPATH = 'filepath';
    const KEY_CONTROLLER = 'apicontroller';
    const KEY_METHOD = 'method';
    const METHOD_PREFIX = 'action';

    private static $instance;

    public static function getInstance(HttpInbound $httpInbound) {

        if (self::$instance) {
            return self::$instance;
        }

        self::$instance = new ApiRouter($httpInbound);
        return self::$instance;
    }

    private $httpInbound;
    private $routes = array(
        array('route' => '', 'filepath' => '', 'apicontroller' => ''),
    );
    private $route = null;
    private $defaultRoute = null;
    public $debug = false;

    public function __construct(HttpInbound $httpInbound) {

        $this->httpInbound = $httpInbound;
    }

    public function setRoutes($routes) {

        $this->routes = $routes;
    }

    public function buildRoute($routeKey, $httpRequestMethod = null) {

        if (empty($httpRequestMethod)) {

            $httpRequestMethod = $this->httpInbound->getServerVar('REQUEST_METHOD', self::HTTP_GET);
        }

        if (empty($routeKey)) {

            $this->route = $this->defaultRoute;
            $this->route[self::KEY_METHOD] = $this->getMethodName($httpRequestMethod);
            return false;
        }

        foreach ($this->routes as $ind => $rt) {

            $regex = "#^{$rt[self::KEY_ROUTE]}\$#";

            if (preg_match($regex, $this->route, $arguments)) {

                array_shift($arguments);
                $this->route = $this->routes[$ind];
                $this->route[self::KEY_METHOD] = $this->getMethodName($httpRequestMethod);
                $this->httpInbound->setRouteArgs($arguments);

                return true;
            }
        }

        $this->route = $this->defaultRoute;
        $this->route[self::KEY_METHOD] = $this->getMethodName($httpRequestMethod);
        return false;
    }

    public function getRoute($routeKey = null) {

        if (empty($routeKey)) {
            $routeKey = $this->httpInbound->getRequestVar(self::routeKey, '/');
        }

        if (empty($this->route)) {

            // build
            $this->buildRoute($routeKey);
        }

        return $this->route;
    }

    public function getMethodName($httpRequestMethod) {

        return self::METHOD_PREFIX . ucfirst(strtolower($httpRequestMethod));
    }

}

/// CORE
interface ApiController {

    public function setHttpStatus();

    public function getHttpStatus();

    public function actionGet();

    public function actionPost();

    public function actionPut();

    public function actionDelete();
}

abstract class AbstractApiController implements ApiController {

    protected $httpStatus = 404;

    public function setHttpStatus($val = 404) {

        $this->httpStatus = $val;
    }

    public function getHttpStatus() {

        return $this->httpStatus;
    }

    public function actionGet() {

        $response = new GenericResponse();

        return json_encode($response);
    }

    public function actionPost() {

        $response = new GenericResponse();

        return json_encode($response);
    }

    public function actionPut() {

        $response = new GenericResponse();

        return json_encode($response);
    }

    public function actionDelete() {

        $response = new GenericResponse();

        return json_encode($response);
    }

}

// CONFIG
class ApiConfig {

    public $routes = array(
        array('route' => '/thing', 'filepath' => 'test.php', 'apicontroller' => 'ThingController'),
        array('route' => '', 'filepath' => '', 'apicontroller' => ''),
    );

    public $defaultRoute = array('route' => '/thing', 'filepath' => '', 'apicontroller' => '');
}

// APP
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

class ThingService {

    public $thing;

    public function __construct($thing) {
        $this->thing = $thing;
    }

}

class ThingView {

    public $x = 0;
    public $y = 0;
    public $width = 0;
    public $height = 0;

}

class ThingData {

    public $id = '1';
    public $status = 'active';
    public $usage = null;

}

class ThingApiController extends AbstractApiController {

}

class ErrorApiController extends AbstractApiController {

}

class Entry {

    public static function main() {

        // bootstrap
        $env = self::bootstrap();

        $entry = new Entry($env);
        $entry->execute();
    }

    private static function bootstrap() {

        $env = new Env();
        $env->setDs(DIRECTORY_SEPARATOR);
        $env->setRoot($_SERVER['DOCUMENT_ROOT']);
        $env->setBase(dirname(__FILE__));
        $env->setAlias(getenv("sd_env"));
        $env->makeReady();

        return $env;
    }

    public function __construct($env) {

        $this->env = $env;
    }

    public function execute() {

        // Route and get class and method from route or error
        $routeArr = $this->route();

        // instantiate class or error
        $controller = $this->buildController($routeArr);

        // execute method and get result
        $method = $routeArr[ApiRouter::KEY_METHOD];
        $data = $controller->$method();

        $this->sendResponse($controller->getHttpStatus(), $data);
    }

    private function route() {

        $apiConfig = new ApiConfig();
        $httpInbound = new HttpInbound();
        $apiRouter = new ApiRouter($httpInbound);
        $apiRouter->setRoutes($apiConfig->routes);

        $routeArr = $apiRouter->getRoute();
var_export($routeArr);
        if (empty($routeArr)) {
            // ERROR
            $this->setError('Route could not be determined', 500);
        }

        return $routeArr;
    }

    private function buildController($routeArr) {

        $filePath = $this->env->getBase($routeArr[ApiRouter::KEY_FILEPATH]);
        if (!file_exists($filePath)) {
            //ERROR
            $this->setError('Controller file could not be found.', 500);
            return null;
        }

        require_once($filePath);

        $class = $routeArr[ApiRouter::KEY_CONTROLLER];

        if (!class_exists($class)) {
            //ERROR
            $this->setError('Controller class could not be found.', 500);
            return null;
        }

        if (!method_exists($class, $routeArr[ApiRouter::KEY_METHOD])) {
            //ERROR
            $this->setError('Controller method could not be found.', 500);
            return null;
        }

        return new $class();
    }

    private function sendResponse($status, $data) {

        HttpResponse::setContentType('application/json');
        // set headers (should be up to controller)
        HttpResponse::setHeader('Access-Control-Allow-Origin', '*');
        HttpResponse::setHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
        HttpResponse::setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
        HttpResponse::setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        HttpResponse::setHeader('Cache-Control', 'post-check=0, pre-check=0', false);
        HttpResponse::setHeader('Pragma', 'no-cache');
        HttpResponse::status($status);
        //
        HttpResponse::setData($data);
        HttpResponse::send();
    }

    private function setError($message = '', $code = 500) {

        throw new Exception($message, $code);
    }

}

Entry::main();