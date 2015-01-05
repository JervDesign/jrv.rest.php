<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php');
/* @todo Build loader
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'ConfigFactory.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'EnvFactory.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'ErrorFactory.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'LogFactory.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'DbFactory.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'RoutesFactory.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'ControllerFactory.php');
 */
foreach (glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . '*.php') as $filename) {
    include $filename;
}

class Application {

    public static function main() {

        self::bootstrap();
        $app = new Application();
        $app->execute();
    }

    public static function bootstrap(){

        $appConfig = new AppConfig();
        
        // need to dynamically set envBase
        $appConfig->envOptions['dirBase'] = dirname(__FILE__);

        // bootstrap
        EnvFactory::build();
        ErrorFactory::build();
        LogFactory::build();
        DbFactory::build();
        RoutesFactory::build();
    }

    public function execute() {

        // Route and get class and method from route or error
        $routeArr = $this->route();

        // instantiate class or error
        $controller = ControllerFactory::build($routeArr);

        // execute method and get result
        $method = $routeArr['method'];
        $controller->$method();
    }

    protected function route($defaultPath = '/', $defaultMethod = 'GET') {

        $httpIn = new HttpIn();
        $routes = Routes::getInstance();

        $requestMethod = $httpIn->readServerVar('REQUEST_METHOD', $defaultMethod);

        $routePath = $httpIn->readGetVar('__route__', $defaultPath);

        $routeArr = $routes->getRoute($routePath, $requestMethod);

        if (empty($routeArr)) {

            Errors::throwNew('Not available.', 404);
            return null;
        }

        return $routeArr;
    }

}

Application::main();
