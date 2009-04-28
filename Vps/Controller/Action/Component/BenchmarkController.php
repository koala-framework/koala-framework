<?php
class Vps_Controller_Action_Component_BenchmarkController extends Vps_Controller_Action
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

    public function indexAction()
    {
        $start = $this->_getParam('start');
        foreach ($this->_rrds as $name=>$rrd) {
            foreach (array_keys($rrd->getGraphs()) as $gName) {
                echo "<img src=\"/admin/component/benchmark/graph?rrd=$name&name=$gName&start=$start\" />";
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function graphAction()
    {
        $rrd = $this->_rrds[$this->_getParam('rrd')];
        $graphs = $rrd->getGraphs();
        $graphs[$this->_getParam('name')]->output($this->_getParam('start'));
    }
}
