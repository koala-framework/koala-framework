<?php
class Vps_Controller_Action_Cli_Svn_SvnStatsController extends Vps_Controller_Action_Cli_Abstract
{
    private $_interval = 1;
    public static function getHelp()
    {
        return "create svn statistics";
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'start',
                'help' => 'start date'
            )
        );
    }

    public function testAction()
    {
        $start = strtotime('2008-01-01');
        $end = strtotime('2008-01-03');
        $interval = 5*60;
        $cmd = "rrdtool create test.rrd ";
        $cmd .= "--start ".($start-1)." ";
        $cmd .= "--step ".($interval)." ";
        $cmd .= "DS:test:COUNTER:".($interval*2).":0:2147483648 ";
        $cmd .= "RRA:AVERAGE:0.5:1:2016 "; //1 woche
        $cmd .= "RRA:MAX:0.5:10:2016 "; //1 woche
        $this->_systemCheckRet($cmd);

        $cnt = 0;
        $i = 0;
        for ($date=$start;$date<$end;$date+=$interval) {
            $i++;
            echo date("d.m.Y H:i:s", $date)." ";
            $cnt += rand(80,100+($i/10));
            $cmd = "rrdtool update test.rrd ";
            $cmd .= "$date:";
            $cmd .= $cnt;
            $this->_systemCheckRet($cmd);
        }

        $cmd = "rrdtool graph test.png -h 600 -w 800 ";
        $cmd .= "-s $start ";
        $cmd .= "-e $end ";
        $cmd .= "DEF:test=test.rrd:test:AVERAGE ";
        $cmd .= "LINE2:test#FF0000 ";
        $cmd .= "DEF:testmax=test.rrd:test:MAX ";
        $cmd .= "LINE2:testmax#00FF00 ";
        $this->_systemCheckRet($cmd);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _escape($web, $type)
    {
//         $web = str_replace('2', 'zwei', $web);
        $web = str_replace('-', '', $web);
        if (strlen($web) > 12) $web = substr($web, 0, 12);
        return "$web$type";
    }

    public function initAction()
    {
        if (!$this->_getParam('filename')) throw new Vps_ClientException("filename not specified");
        $filename = $this->_getParam('filename');

        if (!$this->_getParam('start')) {
            throw new Vps_ClientException("start not specified");
        }
        $webs = $this->_getWebs();

        $start = strtotime($this->_getParam('start'))-1;
        $cmd = "rrdtool create $filename ";
        $cmd .= "--start $start ";
        $cmd .= "--step ".(60*60*24*$this->_interval)." ";
        foreach ($webs as $web) {
            foreach (array('php', 'js', 'css', 'html', 'total', 'test', 'todo') as $type) {
                $name = $this->_escape($web, $type);
                $cmd .= "DS:$name:GAUGE:".(60*60*24*$this->_interval*2).":0:671744 ";
            }
        }
        $cmd .= "RRA:AVERAGE:0.5:1:1000 ";
        $cmd .= "RRA:AVERAGE:0.5:7:1000 ";
        $cmd .= "RRA:AVERAGE:0.5:14:1000 ";
        $this->_systemCheckRet($cmd);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _count($path, $webs, $skipWebs, $date)
    {
        if (!$this->_getParam('filename')) throw new Vps_ClientException("filename not specified");
        $filename = $this->_getParam('filename');

        foreach ($webs as $web) {
            if (in_array($web, $skipWebs)) {
                for($i=0;$i<7;$i++) {
                    $fields[] = 'U';
                }
                continue;
            }
            echo "counting $web ";
            $loc = $this->_loc("$path/$web");
            if (file_exists("$path/$web/tests")) {
                $locTests = $this->_loc("$path/$web/tests");
                foreach ($locTests as $k=>$i) {
                    if (isset($loc[$k])) {
                        $loc[$k] -= $i;
                    }
                }
            }
            foreach ($loc as $k=>$i) {
                echo "$k:$i ";
            }
            foreach (array('php', 'js', 'css', 'html', 'total') as $type) {
                if (isset($loc[$type])) {
                    $fields[] = $loc[$type];
                } else {
                    $fields[] = 'U';
                }
            }
            if (isset($locTests) && isset($locTests['total'])) {
                $fields[] = "$locTests[total]";
                echo "test:$locTests[total] ";
            } else {
                $fields[] = "U";
            }
            $todo = (int)exec("wcgrep -i '//todo' $path/$web | wc -l", $output, $ret);
            if ($ret) d($output);
            $todo += (int)exec("wcgrep -i '// todo' $path/$web | wc -l", $output, $ret);
            if ($ret) d($output);
            $fields[] = $todo;
            echo "todo:$todo\n";
        }
        $cmd = "rrdtool update $filename ";
        $cmd .= "$date:".implode(':', $fields);

        $this->_systemCheckRet($cmd);
    }

    private function _getWebs()
    {
        if (!$this->_getParam('webs')) {
            throw new Vps_ClientException("webs not specified");
        }
        if ($this->_getParam('webs')=='all') {
            exec('svn ls http://svn/trunk/vps-projekte', $out);
            foreach ($out as &$i) {
                $i = trim(trim($i), '/');
            }
            array_unshift($out, 'vps');
            return $out;
        } else {
            return explode(',', $this->_getParam('webs'));
        }
    }

    public function recordAction()
    {
        if (!$this->_getParam('start')) {
            throw new Vps_ClientException("start not specified");
        }
        if ($this->_getParam('path')) {
            $path = $this->_getParam('path');
        } else {
            $path = tempnam('/tmp', 'svnstat');
            unlink($path);
            mkdir($path);
        }
        $webs = $this->_getWebs();

        foreach ($webs as $web) {
            if (file_exists("$path/$web")) continue;
            if ($web == 'vps') {
                $svnPath = "http://svn/trunk/vps";
            } else {
                $svnPath = "http://svn/trunk/vps-projekte/$web";
            }
            echo "checkout $web...\n";
            $cmd = "svn co $svnPath $path/$web >/dev/null";
            $this->_systemCheckRet($cmd);
        }

        for ($date = strtotime($this->_getParam('start')); $date < time(); $date += 24*60*60 * $this->_interval) {
            if (date('Y-m-d', $date) == '2007-11-29') continue; //da is was hin
            echo "update to ".date('Y-m-d', $date);
            $skipWebs = array();
            foreach ($webs as $web) {
                echo ".";
                $cmd = "svn up -r '{".date('Y-m-d', $date)."}' $path/$web >/dev/null 2>&1 ";
                system($cmd, $ret);
                if ($ret) {
                    $skipWebs[] = $web;
                    echo "(skip $web)";
                }
            }
            echo "\n";
            $this->_count($path, $webs, $skipWebs, $date);
        }
        echo "done";
        $this->_helper->viewRenderer->setNoRender(true);
    }

    protected function _systemCheckRet($cmd)
    {
        if ($this->_getParam('debug')) echo "$cmd\n";
        return parent::_systemCheckRet($cmd);
    }

    private function _loc($path)
    {
        $paths = '';
        foreach (new DirectoryIterator($path) as $p) {
            if ($p->isDot()) continue;
            if ($p == '.svn') continue;
            if ($p == 'Zend') continue;
            if ($p == 'Smarty') continue;
            if ($p == 'ext') continue;
            if ($p == 'YUI') continue;
            if ($p == 'images') continue;
            if ($p == 'tcpdf') continue;
            if ($p == 'library') {
                foreach (new DirectoryIterator($path.'/'.$p) as $p2) {
                    if ($p2 == 'Vps' || $p2 == 'files') {
                        $paths .= " $path/$p/$p2";
                    }
                }
                continue;
            }
            $paths .= " $path/$p";
        }
        $cmd = "/home/niko/ohcount-1.0.2/bin/ohcount $paths";


        $descriptorspec = array(
            1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
        );

        $timeLimit = 20;
        $output = '';

        $start = time();
        $process = proc_open($cmd, $descriptorspec, $pipes);
        stream_set_blocking($pipes[1], 0);
        while(!feof($pipes[1])) {
            if (!feof($pipes[1])) {
                $output .= fgets($pipes[1]);
            }
            if (time()-$start > $timeLimit) {
                proc_terminate($process);
                echo "Process '$cmd' killed because timelimit exceeded";
                return array();
            }
        }
        //echo $cmd."\n";
                        //lang   files     code
        preg_match_all('#^([^ ]+) +([0-9]+) +([0-9]+)#m', $output, $m);
        $locs = array();
        foreach($m[1] as $i=>$l) {
            $l = strtolower($l);
            if ($l == 'javascript') $l = 'js';
            if (!isset($locs[$l])) $locs[$l] = 0;
            $locs[$l] += $m[3][$i];
        }
        return $locs;
    }

    public function graphAction()
    {
        if (!$this->_getParam('filename')) throw new Vps_ClientException("filename not specified");
        $filename = $this->_getParam('filename');
        if (!$this->_getParam('start')) throw new Vps_ClientException("start not specified");
        if (!$this->_getParam('end')) {
            $end = time();
        } else {
            $end = strtotime($this->_getParam('end'));
        }

        $colors = array(
            '000000', 'ff0000', '0000ff', '00ff33', 'ff00ff', 'ffcc00', '00ffff',
            'ffff00', 'cc66ff', '6699ff', 'cccc66', 'cc9966', '990000', '999999',
            '9999cc', '339933', 'ff33cc', 'ccff33', 'ff6633'
        );

        if ($this->_getParam('web')) {
            $webs = explode(',', $this->_getParam('web'));
        } else {
            $webs = $this->_getWebs();
        }
        if ($this->_getParam('type')) {
            $types = explode(',', $this->_getParam('type'));
        } else {
            throw new Vps_ClientException("type parameter required");
        }

        $lines = array();
        foreach ($types as $type) {
            foreach ($webs as $web) {
                $lines[] = array('web'=>$web, 'type'=>$type);
            }
        }

        $cmd = "rrdtool graph svnstats-".implode('', $webs)."-".implode('', $types).".png --full-size-mode -h 600 -w 900 ";
        $cmd .= "-s ".strtotime($this->_getParam('start'))." ";
        $cmd .= "-e ".$end." ";
        foreach ($lines as $i=>$l) {
            if (!isset($colors[$i])) {
                $color = '';
                for($j=0;$j<3;$j++) {
                    $p = (string)dechex(rand(0, 255));
                    while(strlen($p) <= 1) $p = "0$p";
                    $color .= strtoupper($p);
                }
            } else {
                $color = $colors[$i];
            }
            $color = strtoupper($color);
            $name = $this->_escape($l['web'], $l['type']);
            $cmd .= "DEF:$name=$filename:$name:AVERAGE ";
            $cmd .= "LINE2:$name#$color:\"$l[web] $l[type]\" ";
        }
        $this->_systemCheckRet($cmd);
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
