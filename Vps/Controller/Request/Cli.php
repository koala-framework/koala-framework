<?php
class Vps_Controller_Request_Cli extends Zend_Controller_Request_Abstract
{
    public function __construct()
    {
        $argv = $_SERVER['argv'];
        unset($argv[0]);
        if (isset($argv[1])) {
            $this->setControllerName($argv[1]);
            unset($argv[1]);
        }
        $this->setActionName('index');

        //todo: reuse any cli-args-parser (der mehr kann)
        //parst im moment nur parameter wie --debug=foo (=foo ist optional)
        $params = array();
        foreach ($argv as $arg) {
            if (substr($arg, 0, 2) == '--') {
                $arg = substr($arg, 2);
                $arg = explode('=', $arg);
                if (!isset($arg[1])) $arg[1] = true;
                $params[$arg[0]] = $arg[1];
            }
        }
        $this->setParams($params);
    }
}
