<?php
class Vps_Controller_Action_Debug_BenchmarkController extends Vps_Controller_Action
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
        echo "<a href=\"/admin/debug/benchmark/values\">current values</a><br />";
        foreach ($this->_rrds as $name=>$rrd) {
            $title = $rrd->getTitle();
            if (!$title) $title = $name;
            echo "<strong>$title:</strong><br />";
            foreach ($rrd->getGraphs() as $gName=>$g) {
                $title = $g->getTitle();
                if (!$title) $title = $gName;
                $n = "{$name}_{$gName}";
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

        foreach ($startDates as $k=>$i) {
            echo "<a href=\"/admin/debug/benchmark?start=$i\"";
            if ($i == $start) echo " style=\"font-weight: bold\"";
            echo ">$k</a> ";
        }
        echo "<br /><br />";
        foreach ($this->_rrds as $name=>$rrd) {
            foreach (array_keys($rrd->getGraphs()) as $gName) {
                $n = "{$name}_{$gName}";
                if (in_array($n, $active)) {
                    echo "<a href=\"/admin/debug/benchmark/detail?rrd=$name&name=$gName\">";
                    echo "<img style=\"border:none\" src=\"/admin/debug/benchmark/graph?rrd=$name&name=$gName&start=".urlencode($start)."\" />";
                    echo "</a>";
                }
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }


    public function detailAction()
    {
        $this->_printReloadJs();
        $rrd = $this->_getParam('rrd');
        $name = $this->_getParam('name');
        echo "<a href=\"/admin/debug/benchmark\">overview</a><br /><br />";
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
        foreach ($startDates as $d) {
            echo "<img src=\"/admin/debug/benchmark/graph?rrd=$rrd&name=$name&start=".urlencode($d)."\" />";
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
        $cacheId = md5('graph_'.$this->_getParam('rrd').'_'.$this->_getParam('name')
                    .'_'.$this->_getParam('start'));
        if (!$output = $cache->load($cacheId)) {
            $rrd = $this->_rrds[$this->_getParam('rrd')];
            $graphs = $rrd->getGraphs();
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
        echo "<a href=\"/admin/debug/benchmark\">graphs</a><br /><br />";
        foreach ($this->_rrds as $rrd) {
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
