<?php
class Vps_Controller_Action_Cli_Web_BenchmarkController extends Vps_Controller_Action_Cli_Abstract
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
            if ($this->_getParam('debug')) {
                $values = array_values($rrd->getRecordValues());
                $cnt = 0;
                foreach (array_values($rrd->getFields()) as $k=>$i) {
                    echo $cnt.': ';
                    echo $i->getText().": ".$values[$k]."\n";
                    $cnt++;
                }
                echo "\n";
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
