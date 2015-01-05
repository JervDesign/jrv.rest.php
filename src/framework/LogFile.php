<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Log.php');

/**
 * Copyright 2013 JervDesign
 * LogFile
 *
 * @author James Jervis
 */
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