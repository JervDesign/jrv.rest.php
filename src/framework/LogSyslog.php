<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Log.php');
/**
 * Copyright 2013 JervDesign
 * LogSyslog
 *
 * @author James Jervis
 */
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