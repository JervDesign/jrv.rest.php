<?php
/**
 * Copyright 2013 JervDesign
 * Log
 *
 * @author James Jervis
 */
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