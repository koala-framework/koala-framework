<?php
class Kwf_Controller_Response_Http extends Zend_Controller_Response_Http
{
    public function sendResponse()
    {
        $gzip = isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false;
        $gzip &= !headers_sent();
        if ($gzip) {
            header('Content-Encoding: gzip');
            ob_start("ob_gzhandler");
        }
        parent::sendResponse();
        if ($gzip) {
            ob_end_flush();
        }
    }
}
