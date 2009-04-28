<?php
class Vps_Controller_Action_Cli_BenchmarkController extends Vps_Controller_Action_Cli_Abstract
{
    private $_rrds;
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_rrds = array();
        foreach (Vps_Registry::get('config')->rrd as $k=>$n) {
            $this->_rrds[$k] = new $n;
        }
    }

    public static function getHelp()
    {
        return "generate benchmark-log statistics";
    }

    public function recordAction()
    {
        foreach ($this->_rrds as $rrd) {
            $rrd->record();
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
