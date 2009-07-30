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
        $this->_rrds = array_reverse($this->_rrds);
    }

    private function _printReloadJs()
    {
        echo "<script type=\"text/javascript\">\n";
        echo "setInterval(function() {
            var imgs = document.getElementsByTagName('img');
            for (var i=0; i<imgs.length; ++i) {
                var img = imgs[i];
                img.src = img.src.replace(/&t=.+/, '')+'&t='+ (new Date()).getTime();
                console.log(img.src);
            }
        }, 1000*60*3);\n";
        echo "</script>\n";
    }

    public function indexAction()
    {
        $this->_printReloadJs();
        echo "<a href=\"/admin/component/benchmark/values\">current values</a><br />";
        $start = $this->_getParam('start');
        $startDates = array(
            'last 3 hours' => time()-3*60*60,
            'last 6 hours' => time()-6*60*60,
            'last 12 hours' => time()-12*60*60,
            'last day' => time()-24*60*60,
            'last week' => time()-7*24*60*60,
            'last month' => strtotime('-1 month'),
            'last 3 months' => strtotime('-3 month'),
            'last 6 months' => strtotime('-6 month'),
            'last year' => strtotime('-1 year')
        );
        if (!$start) $start = $startDates['last week'];
        foreach ($startDates as $k=>$i) {
            echo "<a href=\"/admin/component/benchmark?start=$i\"";
            if ($i == $start) echo " style=\"font-weight: bold\"";
            echo ">$k</a> ";
        }
        echo "<br /><br />";
        foreach ($this->_rrds as $name=>$rrd) {
            foreach (array_keys($rrd->getGraphs()) as $gName) {
                echo "<a href=\"/admin/component/benchmark/detail?rrd=$name&name=$gName\">";
                echo "<img style=\"border:none\" src=\"/admin/component/benchmark/graph?rrd=$name&name=$gName&start=$start\" />";
                echo "</a>";
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }


    public function detailAction()
    {
        $this->_printReloadJs();
        $rrd = $this->_getParam('rrd');
        $name = $this->_getParam('name');
        echo "<a href=\"/admin/component/benchmark\">overview</a><br /><br />";
        $startDates = array(
            'last 3 hours' => time()-3*60*60,
            'last 6 hours' => time()-6*60*60,
            'last 12 hours' => time()-12*60*60,
            'last day' => time()-24*60*60,
            'last week' => time()-7*24*60*60,
            'last month' => strtotime('-1 month'),
            'last 3 months' => strtotime('-3 month'),
            'last 6 months' => strtotime('-6 month'),
            'last year' => strtotime('-1 year')
        );
        foreach ($startDates as $d) {
            echo "<img src=\"/admin/component/benchmark/graph?rrd=$rrd&name=$name&start=$d\" />";
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function graphAction()
    {
        $rrd = $this->_rrds[$this->_getParam('rrd')];
        $graphs = $rrd->getGraphs();
        $graphs[$this->_getParam('name')]->output((int)$this->_getParam('start'));
    }

    public function valuesAction()
    {
        echo "<a href=\"/admin/component/benchmark\">graphs</a><br /><br />";
        foreach ($this->_rrds as $rrd) {
            $values = array_values($rrd->getRecordValues());
            foreach (array_values($rrd->getFields()) as $k=>$i) {
                if ($values[$k] == 'U') continue;
                echo $i->getText().": ".$values[$k];
                echo "<br />";
            }
            echo "<br />";
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
