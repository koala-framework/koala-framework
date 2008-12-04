<?php
class Vps_Controller_Action_Component_BenchmarkController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $start = $this->_getParam('start');
        $graphs = Vps_Controller_Action_Cli_BenchmarkController::getGraphs();
        foreach ($graphs as $gName=>$graph) {
            echo "<img src=\"/admin/component/benchmark/graph?name=$gName&start=$start\" />";
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function graphAction()
    {
        $start = $this->_getParam('start');
        if ($start) {
            $start = strtotime($start);
        }
        if (!$start || $start > time() || (time()-$start) < 1*24*60*60) {
            $start = time()-1*24*60*60;
        }
        $start = max($start, mktime(17,0,0,12,4,2008));

        $c = Vps_Controller_Action_Cli_BenchmarkController::getGraphContent(
            $this->_getParam('name'),
            $start,
            time()
        );
        header('Content-Type: image/png');
        echo $c;
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
