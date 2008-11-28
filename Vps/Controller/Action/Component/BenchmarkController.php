<?php
class Vps_Controller_Action_Component_BenchmarkController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $graphs = Vps_Controller_Action_Cli_BenchmarkController::getGraphs();
        foreach ($graphs as $gName=>$graph) {
            echo "<img src=\"/admin/component/benchmark/graph?name=$gName\" />";
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function graphAction()
    {
        header('Content-Type: image/png');
        echo Vps_Controller_Action_Cli_BenchmarkController::getGraphContent(
            $this->_getParam('name'),
            max(mktime(18,30,0,11,27,2008), time()-1*24*60*60),
            time()
        );
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
