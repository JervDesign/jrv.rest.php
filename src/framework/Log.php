<?php
/**
 * Copyright 2013 JervDesign
 * Log
 *
 * @author James Jervis
 */
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