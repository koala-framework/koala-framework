<?php
class Vps_Controller_Action_Component_BenchmarkController extends Vps_Controller_Action
{
    private $_rrds;
    protected function _getRrds()
    {
        if (!isset($this->_rrds)) {
            $this->_rrds = array();
            foreach (Vps_Registry::get('config')->rrd as $k=>$n) {
                $this->_rrds[$k] = new $n;
            }
            $this->_rrds = array_reverse($this->_rrds);
        }
        return $this->_rrds;
    }

    protected function _getGraphs()
    {
        $ret = array();
        foreach ($this->_getRrds() as $name=>$rrd) {
            foreach ($rrd->getGraphs() as $gName=>$graph) {
                $ret[$name.'_'.$gName] = $graph;
            }
        }
        return $ret;
    }

    private function _printReloadJs()
    {
        echo "<script type=\"text/javascript\">\n";
        echo "setInterval(function() {
            var imgs = document.getElementsByTagName('img');
            for (var i=0; i<imgs.length; ++i) {
                var img = imgs[i];
                img.src = img.src.replace(/&t=.+/, '')+'&t='+ (new Date()).getTime();
            }
        }, 1000*60*5+10);\n";
        echo "</script>\n";
    }

    public function indexAction()
    {
        ob_start(); //wegen setcookie

        $active = array();
        if (isset($_REQUEST['submit'])) {
        } else if (isset($_COOKIE['benchmark-active'])) {
            $active = explode('**', $_COOKIE['benchmark-active']);
        }
        $this->_printReloadJs();
        echo "<form action=\"\" method=\"post\" style=\"position: absolute; right:0; top:0; text-align:right; margin-right:10px;\">";
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'values'));
        echo "<a href=\"$url\">current values</a><br />";
        foreach ($this->_getGraphs() as $n=>$g) {
            $title = $g->getTitle();
            if (!$title) $title = $n;
            echo "<label for=\"$n\">$title</label>";
            echo "<input id=\"$n\" name=\"$n\" type=\"checkbox\" ";
            if (isset($_REQUEST[$n])) {
                if (!in_array($n, $active)) {
                    $active[] = $n;
                }
            }
            if (in_array($n, $active)) {
                echo "checked=\"checked\" ";
            }
            echo "/><br />";
        }
        setcookie('benchmark-active', implode('**', $active), time()+60*60*24*14);
        echo "<input type=\"submit\" name=\"submit\">\n";
        echo "</form>\n";
        $startDates = array(
            'last 3 hours' => '-3 hours',
            'last 6 hours' => '-6 hours',
            'last 12 hours' => '-12 hours',
            'last day' => '-1 day',
            'last week' => '-1 week',
            'last month' => '-1 month',
            'last 3 months' => '-3 month',
            'last 6 months' => '-6 month',
            'last year' => '-1 year'
        );
        $start = $this->_getParam('start');
        if (!$start && isset($_COOKIE['start'])) $start = $_COOKIE['start'];
        if (!$start || !strtotime($start)) $start = '-1 week';
        setcookie('benchmark-start', $start, time()+60*60*24*14);

        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index'));
        foreach ($startDates as $k=>$i) {
            echo "<a href=\"$url?start=$i\"";
            if ($i == $start) echo " style=\"font-weight: bold\"";
            echo ">$k</a> ";
        }
        echo "<br /><br />";
        foreach ($this->_getGraphs() as $n=>$graph) {
            if (in_array($n, $active)) {
                $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'detail'));
                echo "<a href=\"$url?name=$n\">";

                $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'graph'));
                echo "<img style=\"border:none\" src=\"$url?name=$n&start=".urlencode($start)."\" />";
                echo "</a>";
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }


    public function detailAction()
    {
        $this->_printReloadJs();
        $name = $this->_getParam('name');
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index'));
        echo "<a href=\"$url\">overview</a><br /><br />";
        $startDates = array(
            'last 3 hours' => '-3 hours',
            'last 6 hours' => '-6 hours',
            'last 12 hours' => '-12 hours',
            'last day' => '-1 day',
            'last week' => '-1 week',
            'last month' => '-1 month',
            'last 3 months' => '-3 month',
            'last 6 months' => '-6 month',
            'last year' => '-1 year'

        );
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'graph'));
        foreach ($startDates as $d) {
            echo "<img src=\"$url?name=$name&start=".urlencode($d)."\" />";
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function graphAction()
    {
        $frontendOptions = array(
            'lifetime' => 60*5,
            'automatic_serialization' => true
        );
        $backendOptions = array(
            'cache_dir' => 'application/cache/benchmark/'
        );
        $cache = Vps_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        $cacheId = md5('graph_'.$this->_getParam('name').'_'.$this->_getParam('start'));
        if (!$output = $cache->load($cacheId)) {
            $graphs = $this->_getGraphs();
            $g = $graphs[$this->_getParam('name')];
            $output = array();
            $start = strtotime($this->_getParam('start'));
            if (!$start) {
                throw new Vps_ClientException("invalid start");
            }
            $output['contents'] = $g->getContents($start);
            $output['mimeType'] = 'image/png';
            $cache->save($output, $cacheId);
        }
        Vps_Media_Output::output($output);
    }

    public function valuesAction()
    {
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index'));
        echo "<a href=\"$url\">graphs</a><br /><br />";
        foreach ($this->_getRrds() as $rrd) {
            $values = array_values($rrd->getRecordValues());
            $cnt = 0;
            foreach (array_values($rrd->getFields()) as $k=>$i) {
                if ($values[$k] == 'U') $values[$k] = '?';
                echo $cnt.': ';
                echo $i->getText().": ".$values[$k];
                echo "<br />";
                $cnt++;
            }
            echo "<br />";
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
