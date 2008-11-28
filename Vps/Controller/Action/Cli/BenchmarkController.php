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
            'preload cache', 'rendered nocache', 'rendered cache (preloaded)',
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

    private static function _escapeField($f)
    {
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
                'content-requests' => array(
                    'perRequest' => false,
                    'color' => '#000000',
                    'label' => 'content'
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
                )
            ),
            'cache'=>array(
                'verticalLabel' => 'components rendered / request',
                'content-rendered nocache' => array(
                    'color' => '#FF0000',
                    'label' => 'nocache'
                ),
                'content-rendered cache (preloaded)' => array(
                    'color' => '#00FF00',
                    'label' => 'cache'
                )
            ),
            'duration'=>array(
                'verticalLabel' => 'processing time / request [s]',
                'content-duration' => array(
                    'cmd' => 'CDEF:perrequestx0=perrequest0,1000,/',
                    'line' => 'LINE2:perrequestx0#FF0000:"content" ',
                ),
                'media-duration' => array(
                    'cmd' => 'CDEF:perrequestx1=perrequest1,1000,/',
                    'line' => 'LINE2:perrequestx1#00FF00:"media" ',
                ),
                'asset-duration' => array(
                    'cmd' => 'CDEF:perrequestx2=perrequest2,1000,/',
                    'line' => 'LINE2:perrequestx2#000000:"asset" ',
                ),
                'admin-duration' => array(
                    'cmd' => 'CDEF:perrequestx3=perrequest3,1000,/',
                    'line' => 'LINE2:perrequestx3#0000FF:"admin" ',
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
            foreach ($this->_getFields() as $field) {
                $cmd .= "DS:".self::_escapeField($field).":COUNTER:".($interval*2).":0:2147483648 ";
            }
            $cmd .= "RRA:AVERAGE:0.6:1:2016 "; //1 woche
            $cmd .= "RRA:AVERAGE:0.6:7:1500 "; //1 Monat
            $cmd .= "RRA:AVERAGE:0.6:50:2500 "; //1 Jahr
            $this->_systemCheckRet($cmd);
        }

        $values = array();
        foreach ($this->_getFields() as $field) {
            $memcache = new Memcache;
            $memcache->addServer('localhost');

            $prefix = Zend_Registry::get('config')->application->id.'-'.
                                Vps_Setup::getConfigSection().'-bench-';
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
        if (!$this->_getParam('end')) throw new Vps_ClientException("end not specified");
        $start = strtotime($this->_getParam('start'));
        $end = strtotime($this->_getParam('end'));

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
        $cmd = "rrdtool graph $tmpFile -h 768 -w 1024 ";
        $cmd .= "-s $start ";
        $cmd .= "-e $end ";
        if (isset($graph['verticalLabel'])) {
            $cmd .= "--vertical-label \"$graph[verticalLabel]\" ";
        }
        $cmd .= "DEF:requests=benchmark.rrd:".self::_escapeField('content-requests').":AVERAGE ";
        $i = 0;
        foreach ($graph as $field=>$settings) {
            if ($field == 'verticalLabel') continue;
            $cmd .= "DEF:line$i=benchmark.rrd:".self::_escapeField($field).":AVERAGE ";
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
        exec($cmd, $out, $ret);
        if ($ret) {
            throw new Vps_Exception($out);
        }
        $ret = file_get_contents($tmpFile);
        unlink($tmpFile);
        return $ret;
    }
}
