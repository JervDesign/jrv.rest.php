<?php

/**
 * Copyright 2013 JervDesign
 * ControllerFactory
 *
 * @author James Jervis
 */
class ControllerFactory {

    public static function build($routeArr) {

        $filePath = Env::getBase($routeArr['classPath']);

        if (!file_exists($filePath)) {
            //ERROR
            Log::write("Controller file was not found: {$filePath}", 404, 'ERR', __method__);
            Error::throwNew('Not found.', 404);
            return null;
        }

        require_once($filePath);

        $class = $routeArr['class'];

        if (!class_exists($class)) {
            //ERROR
            Log::write("Controller class was not found: filepath: {$filePath}, class: {$class}", 404, 'ERR', __method__);
            Error::throwNew('Not found.', 404);
            return null;
        }

        if (!method_exists($class, $routeArr['method'])) {
            //ERROR
            Log::write("Controller method was not found: filepath: {$filePath}, class: {$class}, method: {$method}", 404, 'ERR', __method__);
            Error::throwNew('Not found.', 404);
            return null;
        }

        $obj = new $class();
        $obj->setArgs($routeArr['args']);

        return $obj;
    }

}