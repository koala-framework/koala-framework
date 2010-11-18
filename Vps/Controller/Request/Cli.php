<?php
class Vps_Controller_Request_Cli extends Zend_Controller_Request_Abstract
{
    public function __construct()
    {
        $argv = $_SERVER['argv'];
        unset($argv[0]);
        foreach ($argv as $k=>$i) {
            if (substr($i, 0, 2) == '--') {
                unset($argv[$k]);
            }
        }
        $argv = array_values($argv);
        if (isset($argv[0])) {
            $this->setControllerName($argv[0]);
        }
        if (isset($argv[1])) {
            $this->setActionName($argv[1]);
        } else {
            $this->setActionName('index');
        }

        $argv = $_SERVER['argv'];
        unset($argv[0]);
        //todo: reuse any cli-args-parser (der mehr kann)
        //parst im moment nur parameter wie --debug=foo (=foo ist optional)
        $params = array();
        foreach ($argv as $arg) {
            if (substr($arg, 0, 2) == '--') {
                $arg = substr($arg, 2);
                $arg = explode('=', $arg);
                $p = $arg[0];
                if (!isset($arg[1])) {
                    $params[$p] = true;
                } else {
                    unset($arg[0]);
                    $params[$p] = implode('=', $arg);
                }
            }
        }
        $this->setParams($params);
    }
    public function getResourceName()
    {
        return 'vps_cli';
    }
}
