<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php');

class ConfigFactory {

    public static function build() {

        return new AppConfig();
    }

}

class EnvFactory {

    public static function build() {

        $config = ConfigFactory::build();

        Env::setDs(DIRECTORY_SEPARATOR);
        Env::setRoot($_SERVER['DOCUMENT_ROOT']);
        Env::setBase(dirname(__FILE__));

        $options = $config->get('envOptions', array());

        if (isset($options['envVar'])) {

            Env::setAlias(getenv($config->envOptions['envVar']));
        }

        Env::makeReady();

        return true;
    }

}

class ErrorFactory {

    public static function build() {

        $config = ConfigFactory::build();
        $class = $config->get('errorClass');
        $options = $config->get('errorOptions', array());

        if (empty($class)) {
            // ERROR
            return false;
        }

        if (class_exists($class)) {

            Errors::setReporter(new $class($options));

            return true;
        } else {
            //ERROR
            return false;
        }
    }

}

class LogFactory {

    public static function build() {

        $config = ConfigFactory::build();
        $class = $config->get('logClass');
        $options = $config->get('logOptions', array());

        if (empty($class)) {
            // ERROR
            return false;
        }

        if (class_exists($class)) {

            Log::setLogger(new $class($options));

            return true;
        } else {
            //ERROR
            return false;
        }
    }

}

class DbFactory {

    public static function build() {

        $config = ConfigFactory::build();
        $class = $config->get('dbClass');
        $options = $config->get('dbOptions', array());

        if (empty($class)) {
            // ERROR
            return false;
        }

        if (class_exists($class)) {

            Db::setConnection(new $class($options));

            return true;
        } else {
            //ERROR
            return false;
        }
    }

}

class RoutesFactory {

    public static function build() {

        $config = ConfigFactory::build();
        $class = $config->get('routesClass');
        $options = $config->get('routesOptions', array());

        if (empty($class)) {
            // ERROR
            return false;
        }

        if (class_exists($class)) {

            Routes::setParser(new $class($options));

            return true;
        } else {
            //ERROR
            return false;
        }
    }

}

class ControllerFactory {

    public static function build($routeArr) {

        $filePath = Env::getBase($routeArr['classPath']);

        if (!file_exists($filePath)) {
            //ERROR
            Log::write("Controller file was not found: {$filePath}", 404, 'ERR', __method__);
            Errors::throwNew('Not found.', 404);
            return null;
        }

        require_once($filePath);

        $class = $routeArr['class'];

        if (!class_exists($class)) {
            //ERROR
            Log::write("Controller class was not found: filepath: {$filePath}, class: {$class}", 404, 'ERR', __method__);
            Errors::throwNew('Not found.', 404);
            return null;
        }

        if (!method_exists($class, $routeArr['method'])) {
            //ERROR
            Log::write("Controller method was not found: filepath: {$filePath}, class: {$class}, method: {$method}", 404, 'ERR', __method__);
            Errors::throwNew('Not found.', 404);
            return null;
        }

        $obj = new $class();
        $obj->setArgs($routeArr['args']);

        return $obj;
    }

}
