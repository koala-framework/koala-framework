<?php
class Kwf_Benchmark_Rrd extends Kwf_Util_Rrd_File
{
    public function getTitle()
    {
        return trlKwf('Kwf Benchmark');
    }

    public function __construct()
    {
        parent::__construct('benchmark.rrd');
        $this->addField(array(
            'name'=>'load',
            'type'=>'GAUGE',
            'max'=>1000,
        ));
        $this->addField(array(
            'name'=>'bytesRead',
            'max'=>pow(2, 64),
        ));
        $this->addField(array(
            'name'=>'bytesWritten',
            'max'=>pow(2, 64),
        ));
        $this->addField('getHits');
        $this->addField('getMisses');
        foreach ($this->_getFieldNames() as $field) {
            $this->addField($field);
        }
        $this->addField(array(
            'name'=>'mysql-processes',
            'type'=>'GAUGE',
            'max'=>1000,
        ));
        $this->addField(array(
            'name'=>'mysql-processes-select',
            'type'=>'GAUGE',
            'max'=>1000,
        ));
        $this->addField(array(
            'name'=>'mysql-processes-modify',
            'type'=>'GAUGE',
            'max'=>1000,
        ));
        $this->addField(array(
            'name'=>'mysql-processes-others',
            'type'=>'GAUGE',
            'max'=>1000,
        ));
        $this->addField(array(
            'name'=>'mysql-processes-locked',
            'type'=>'GAUGE',
            'max'=>1000,
        ));
        $this->addField(array(
            'name'=>'memcache-bytes',
            'type'=>'GAUGE',
            'max'=>pow(2, 64),
        ));
        $this->addField(array(
            'name'=>'memcache-curr-items',
            'type'=>'GAUGE',
            'max'=>pow(2, 64),
        ));
        $this->addField(array(
            'name'=>'memcache-curr-connections',
            'type'=>'GAUGE',
            'max'=>pow(2, 64),
        ));
        $this->addField(array(
            'name'=>'memcache-limit-maxbytes',
            'type'=>'GAUGE',
            'max'=>pow(2, 64),
        ));
    }

    private function _getFieldNames()
    {
        $contentFields = array(
            'requests', 'duration', 'queries',
            'componentDatas', 'generators', 'componentData Pages', 'components',
            'preload cache', 'rendered nocache', 'rendered cache',
            'getRecursiveChildComponents', 'getChildComponents uncached', 'getChildComponents cached', 'countChildComponents',
            'Generator::getInst semi-hit', 'Generator::getInst miss', 'Generator::getInst hit',
            'processing dependencies miss', 'rendered noviewcache',
            'rendered partial cache', 'rendered partial nocache', 'rendered partial noviewc'
        );
        $fields = array();
        foreach ($contentFields as $f) {
            $fields[] = 'content-'.$f;
        }
        $fields[] = 'media-requests';
        $fields[] = 'admin-requests';
        $fields[] = 'admin-duration';
        $fields[] = 'admin-queries';
        $fields[] = 'asset-requests';
        $fields[] = 'cli-requests';
        $fields[] = 'unkown-requests';
        return $fields;
    }

    public function getRecordValues()
    {
        $values = array();
        $load = @file_get_contents('/proc/loadavg');
        $load = explode(' ', $load);
        $values[] = $load[0];

        if (Kwf_Cache_Simple::$memcacheHost) {
            $counter = new Kwf_Benchmark_Counter_Memcache();
            $memcache = $counter->getMemcache();
            $memcacheStats = $memcache->getStats();
            $values[] = $memcacheStats['bytes_read'];
            $values[] = $memcacheStats['bytes_written'];
            $values[] = $memcacheStats['get_hits'];
            $values[] = $memcacheStats['get_misses'];
            foreach ($this->_getFieldNames() as $field) {
                $values[] = $this->_getMemcacheValue($field);
            }
        } else {
            $values[] = 'U';
            $values[] = 'U';
            $values[] = 'U';
            $values[] = 'U';
            foreach ($this->_getFieldNames() as $field) {
                $values[] = 'U';
            }
        }

        $cnt = array(
            'processes' => 0,
            'select' => 0,
            'modify' => 0,
            'others' => 0,
            'locked' => 0
        );
        if (Kwf_Registry::get('dao')) {
            $dbConfig = Kwf_Registry::get('dao')->getDbConfig();
            $dbName = $dbConfig['dbname'];
            foreach (Kwf_Registry::get('db')->query('SHOW PROCESSLIST')->fetchAll() as $row) {
                if ($row['db'] != $dbName) continue;
                if ($row['Command'] == 'Sleep') continue;
                $sql = strtolower(trim($row['Info']));
                if ($sql == 'show processlist') continue;
                $cnt['processes']++;
                if ($row['Command'] == 'Locked') {
                    $cnt['locked']++;
                }
                if (substr($sql, 0, 6) == 'select') {
                    $cnt['select']++;
                } else if (substr($sql, 0, 6) == 'update'
                    || substr($sql, 0, 6) == 'insert'
                    || substr($sql, 0, 7) == 'replace'
                    || substr($sql, 0, 6) == 'delete'
                ) {
                    $cnt['modify']++;
                } else {
                    $cnt['others']++;
                }
            }
        }
        $values = array_merge($values, array_values($cnt));

        if (Kwf_Cache_Simple::$memcacheHost) {
            $values[] = $memcacheStats['bytes'];
            $values[] = $memcacheStats['curr_items'];
            $values[] = $memcacheStats['curr_connections'];
            $values[] = $memcacheStats['limit_maxbytes'];
        } else {
            $values[] = 'U';
            $values[] = 'U';
            $values[] = 'U';
            $values[] = 'U';
        }
        return $values;
    }

    public function getGraphs()
    {
        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setVerticalLabel('requests / s');
        $g->addField('asset-requests', '#00FF00', 'assets');
        $g->addField('cli-requests', '#999999', 'cli');
        $g->addField('unkown-requests', '#FF0000', 'unkown');
        $g->addField('media-requests', '#00FFFF', 'media');
        $g->addField('admin-requests', '#0000FF', 'admin');
        $g->addField('content-requests', '#000000', 'content');
        $ret['requests'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setDevideBy('content-requests');
        $g->setVerticalLabel('components rendered / request');
        $g->addField('content-rendered cache', '#00FF00', 'cache');
        $g->addField('content-rendered noviewcache', '#000000', 'noviewcache');
        $g->addField('content-rendered partial cache', '#FFFF00', 'partial cache');
        $g->addField('content-rendered partial nocache', '#00FFFF', 'partial nocache');
        $g->addField('content-rendered partial noviewc', '#FF00FF', 'partial noviewcache');
        $g->addField('content-rendered nocache', '#FF0000', 'nocache');
        $ret['cache'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setVerticalLabel('processing time / request [s]');
        $g->setDevideBy('content-requests');
        $g->addField('admin-duration', array(
            'cmd' => 'CDEF:perrequestx0=perrequest0,1000,/',
            'line' => 'LINE2:perrequestx0#0000FF:"admin"'
        ));
        $g->addField('content-duration', array(
            'cmd' => 'CDEF:perrequestx1=perrequest1,1000,/',
            'line' => 'LINE2:perrequestx1#FF0000:"content" '
        ));
        $ret['duration'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setDevideBy('content-requests');
        $g->setVerticalLabel('objects / request');
        $g->addField('content-componentDatas', '#FF0000');
        $g->addField('content-componentData Pages', '#00FF00');
        $g->addField('content-generators', '#0000FF');
        $g->addField('content-components', '#00FFFF');
        $ret['objects'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setDevideBy('content-requests');
        $g->setVerticalLabel('calls / request');
        $g->addField('content-getChildComponents uncached', '#FF0000');
        $g->addField('content-getChildComponents cached', '#00FF00');
        $g->addField('content-getRecursiveChildComponents', '#000000');
        $g->addField('content-countChildComponents', '#666666');
        $ret['getChildComponents'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setDevideBy('content-requests');
        $g->setVerticalLabel('Generator::getInst calls / request');
        $g->addField('content-Generator::getInst semi-hit', '#0000FF', 'semi-hit');
        $g->addField('content-Generator::getInst miss', '#FF0000', 'miss');
        $g->addField('content-Generator::getInst hit', '#00FF00', 'hit');
        $ret['generators'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setTitle('Memcache');
        $g->setVerticalLabel('accesses');
        $g->addField('getHits', '#00FF00');
        $g->addField('getMisses', '#FF0000');
        $ret['memcacheHits'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setTitle('Memcache');
        $g->setVerticalLabel('bytes');
        $g->addField('bytesRead');
        $g->addField('bytesWritten');
        $ret['memcacheBytes'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setTitle('Memcache');
        $g->setVerticalLabel('bytes');
        $g->addField('memcache-bytes');
        $g->addField('memcache-limit-maxbytes');
        $ret['memcacheBytesAbs'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setTitle('Memcache');
        $g->setVerticalLabel('items');
        $g->addField('memcache-curr-items');
        $ret['memcacheItems'] = $g;


        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setTitle('Memcache');
        $g->setVerticalLabel('connections');
        $g->addField('memcache-curr-connections');
        $ret['memcacheConnections'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setVerticalLabel('[load]');
        $g->setLowerLimit(0);
        $g->addField('load', '#000000');
        $ret['load'] = $g;

        $g = new Kwf_Util_Rrd_Graph($this);
        $g->setVerticalLabel('[processes]');
        $g->setLowerLimit(0);
        $g->addField('mysql-processes');
        $g->addField('mysql-processes-select');
        $g->addField('mysql-processes-modify');
        $g->addField('mysql-processes-others');
        $g->addField('mysql-processes-locked');
        $ret['mysql'] = $g;

        return $ret;
    }
}
