<?php
class Vps_Controller_Action_Cli_BenchmarkController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "generate benchmark-log statistics";
    }

    private function _getFields()
    {
        $ret = array();
        $fields = array(
            'requests', 'duration', 'queries',
            'componentDatas', 'generators', 'componentData Pages', 'components',
            'preload cache', 'rendered nocache', 'rendered cache', 'rendered noviewcache',
            'getRecursiveChildComponents', 'getChildComponents uncached', 'getChildComponents cached', 'countChildComponents',
            'iccc cache semi-hit', 'iccc cache miss', 'iccc cache hit',
            'Generator::getInst semi-hit', 'Generator::getInst miss', 'Generator::getInst hit',
            'processing dependencies miss',
        );
        foreach (array('content', 'media', 'admin', 'asset', 'cli', 'unkown') as $t) {
            foreach ($fields as $f) {
                $ret[] = $t.'-'.$f;
            }
        }
        return $ret;

    }

    public static function escapeField($f)
    {
        if (in_array($f, array('getHits', 'getMisses', 'bytesRead', 'bytesWritten'))) return $f;

        $ret = strtolower(preg_replace('#[^a-zA-Z]#', '', $f));
        if (strlen($ret) > 19) {
            $ret = substr($ret, 0, 10).substr($ret, -9);
        }
        return $ret;
    }

    public static function getGraphs()
    {
        $graphs = array(
            'requests'=>array(
                'verticalLabel' => 'requests / s',
                'asset-requests' => array(
                    'perRequest' => false,
                    'color' => '#00FF00',
                    'label' => 'assets'
                ),
                'cli-requests' => array(
                    'perRequest' => false,
                    'color' => '#999999',
                    'label' => 'cli'
                ),
                'unkown-requests' => array(
                    'perRequest' => false,
                    'color' => '#FF0000',
                    'label' => 'unkown'
                ),
                'media-requests' => array(
                    'perRequest' => false,
                    'color' => '#00FFFF',
                    'label' => 'media'
                ),
                'admin-requests' => array(
                    'perRequest' => false,
                    'color' => '#0000FF',
                    'label' => 'admin'
                ),
                'content-requests' => array(
                    'perRequest' => false,
                    'color' => '#000000',
                    'label' => 'content'
                ),

            ),
            'cache'=>array(
                'verticalLabel' => 'components rendered / request',
                'content-rendered nocache' => array(
                    'color' => '#FF0000',
                    'label' => 'nocache'
                ),
                'content-rendered cache' => array(
                    'color' => '#00FF00',
                    'label' => 'cache'
                ),
                'content-rendered noviewcache' => array(
                    'color' => '#000000',
                    'label' => 'noviewcache'
                )
            ),
            'duration'=>array(
                'verticalLabel' => 'processing time / request [s]',
                'asset-duration' => array(
                    'cmd' => 'CDEF:perrequestx0=perrequest0,1000,/',
                    'line' => 'LINE2:perrequestx0#000000:"asset" ',
                ),
                'media-duration' => array(
                    'cmd' => 'CDEF:perrequestx1=perrequest1,1000,/',
                    'line' => 'LINE2:perrequestx1#00FF00:"media" ',
                ),
                'admin-duration' => array(
                    'cmd' => 'CDEF:perrequestx2=perrequest2,1000,/',
                    'line' => 'LINE2:perrequestx2#0000FF:"admin" ',
                ),
                'content-duration' => array(
                    'cmd' => 'CDEF:perrequestx3=perrequest3,1000,/',
                    'line' => 'LINE2:perrequestx3#FF0000:"content" ',
                ),
            ),
            'objects'=>array(
                'verticalLabel' => 'objects / request',
                'content-componentDatas' => array(
                    'color' => '#FF0000',
                ),
                'content-componentData Pages' => array(
                    'color' => '#00FF00',
                ),
                'content-generators' => array(
                    'color' => '#0000FF',
                ),
                'content-components' => array(
                    'color' => '#00FFFF',
                ),
            ),
            'iccc' => array(
                'verticalLabel' => 'calls / request',
                'content-iccc cache semi-hit' => array(
                    'color' => '#00FF00'
                ),
                'content-iccc cache miss' => array(
                    'color' => '#FF0000'
                ),
                'content-iccc cache hit' => array(
                    'color' => '#0000FF'
                ),
            ),
            'getChildComponents' => array(
                'verticalLabel' => 'calls / request',
                'content-getChildComponents uncached' => array(
                    'color' => '#FF0000'
                ),
                'content-getChildComponents cached' => array(
                    'color' => '#00FF00'
                ),
                'content-getRecursiveChildComponents' => array(
                    'color' => '#000000'
                ),
                'content-countChildComponents' => array(
                    'color' => '#666666'
                ),
            ),
            'generators' => array(
                'verticalLabel' => 'Generator::getInst calls / request',
                'content-Generator::getInst semi-hit' => array(
                    'color' => '#0000FF',
                    'label' => 'semi-hit'
                ),
                'content-Generator::getInst miss' => array(
                    'color' => '#FF0000',
                    'label' => 'miss'
                ),
                'content-Generator::getInst hit' => array(
                    'color' => '#00FF00',
                    'label' => 'hit'
                ),
            ),
            'memcacheHits' => array(
                'verticalLabel' => 'accesses',
                'getHits' => array(
                    'color' => '#00FF00',
                ),
                'getMisses' => array(
                    'color' => '#FF0000',
                ),
            ),
            /*
            'memcacheBytes' => array(
                'verticalLabel' => '[bytes]',
                'bytesRead' => array(
                    'color' => '#00FF00',
                ),
                'bytesWritten' => array(
                    'color' => '#FF0000',
                ),
            ),
            */
            'load' => array(
                'verticalLabel' => '[load]',
                'load' => array(
                    'color' => '#000000',
                ),
            )
        );
        return $graphs;
    }

    public function recordAction()
    {
        if (!file_exists('benchmark.rrd')) {
            $interval = 60;
            $cmd = "rrdtool create benchmark.rrd ";
            $cmd .= "--start ".(time()-1)." ";
            $cmd .= "--step ".($interval)." ";
            $cmd .= "DS:load:ABSOLUTE:".($interval*2).":0:1000 ";
            $cmd .= "DS:bytesRead:COUNTER:".($interval*2).":0:".(2^64)." ";
            $cmd .= "DS:bytesWritten:COUNTER:".($interval*2).":0:".(2^64)." ";
            $cmd .= "DS:getHits:COUNTER:".($interval*2).":0:".(2^31)." ";
            $cmd .= "DS:getMisses:COUNTER:".($interval*2).":0:".(2^31)." ";
            foreach ($this->_getFields() as $field) {
                $cmd .= "DS:".self::escapeField($field).":COUNTER:".($interval*2).":0:".(2^31)." ";
            }
            $cmd .= "RRA:AVERAGE:0.6:1:2016 "; //1 woche
            $cmd .= "RRA:AVERAGE:0.6:7:1500 "; //1 Monat
            $cmd .= "RRA:AVERAGE:0.6:50:2500 "; //1 Jahr
            $this->_systemCheckRet($cmd);
        }

        $values = array();
        $load = @file_get_contents('/proc/loadavg');
        $load = explode(' ', $load);
        $values[] = $load[0];
        $memcache = new Memcache;
        $memcacheSettings = Vps_Registry::get('config')->server->memcache;
        $memcache->addServer($memcacheSettings->host, $memcacheSettings->port);
        $stats = $memcache->getStats();
        $values[] = $stats['bytes_read'];
        $values[] = $stats['bytes_written'];
        $values[] = $stats['get_hits'];
        $values[] = $stats['get_misses'];
        $prefix = Zend_Registry::get('config')->application->id.'-'.
                            Vps_Setup::getConfigSection().'-bench-';
        foreach ($this->_getFields() as $field) {
            $value = $memcache->get($prefix.$field);
            if ($value===false) $value = 'U';
            $values[] = $value;
        }
        $cmd = "rrdtool update benchmark.rrd ";
        $cmd .= "N:";
        $cmd .= implode(':', $values);
        $this->_systemCheckRet($cmd);
        $this->_helper->viewRenderer->setNoRender(true);
    }
    public function graphAction()
    {
        if (!$this->_getParam('start')) throw new Vps_ClientException("start not specified");
        if (!$this->_getParam('end')) {
            $end = time();
        } else {
            $end = strtotime($this->_getParam('end'));
        }
        $start = strtotime($this->_getParam('start'));

        foreach (self::getGraphs() as $gName=>$graph) {
            echo "benchmark-$gName.png\n";
            $c = self::getGraphContent($gName, $start, $end);
            file_put_contents("benchmark-$gName.png", $c);
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public static function getGraphContent($gName, $start, $end)
    {
        $graphs = self::getGraphs();
        $graph = $graphs[$gName];
        $tmpFile = tempnam('/tmp', 'graph');
        $cmd = "rrdtool graph $tmpFile -h 300 -w 600 ";
        $cmd .= "-s $start ";
        $cmd .= "-e $end ";
        if (isset($graph['verticalLabel'])) {
            $cmd .= "--vertical-label \"$graph[verticalLabel]\" ";
        }
        $cmd .= "DEF:requests=benchmark.rrd:".self::escapeField('content-requests').":AVERAGE ";
        $i = 0;
        foreach ($graph as $field=>$settings) {
            if ($field == 'verticalLabel') continue;
            $cmd .= "DEF:line$i=benchmark.rrd:".self::escapeField($field).":AVERAGE ";
            $cmd .= "CDEF:perrequest$i=line$i,requests,/ ";
            if (isset($settings['cmd'])) $cmd .= $settings['cmd'].' ';
            if (isset($settings['line'])) {
                $cmd .= $settings['line'].' ';
            } else {
                if (isset($settings['label'])) {
                    $label = $settings['label'];
                } else {
                    $label = str_replace('content-', '', $field);
                    $label = str_replace('::', '-', $label);
                }
                if (isset($settings['perRequest']) && !$settings['perRequest']) {
                    $fieldName = "line$i";
                } else {
                    $fieldName = "perrequest$i";
                }
                $cmd .= "LINE2:$fieldName$settings[color]:\"$label\" ";
            }
            $i++;
        }
        $cmd .= " 2>&1";
        exec($cmd, $out, $ret);
        if ($ret) {
            throw new Vps_ClientException(implode('', $out));
        }
        $ret = file_get_contents($tmpFile);
        unlink($tmpFile);
        return $ret;
    }
}
