<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'HttpOut.php');
/**
 * Copyright 2013 JervDesign
 * HttpUtils
 *
 * @author James Jervis
 */
class HttpUtils {

    public static function setHeadersNoCache() {

        HttpOut::setHeader('Access-Control-Allow-Origin', '*');
        HttpOut::setHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
        HttpOut::setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
        HttpOut::setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        HttpOut::setHeader('Cache-Control', 'post-check=0, pre-check=0', false);
        HttpOut::setHeader('Pragma', 'no-cache');
    }

    public static function setJsonContentType() {

        HttpOut::setContentType('application/json');
    }

    public static function sendJsonResponse($status, $data) {
        
        HttpOut::setContentType('application/json');
        // set headers (should be up to controller)
        HttpOut::status($status);
        //
        HttpOut::setData($data);
        HttpOut::send();
    }

}