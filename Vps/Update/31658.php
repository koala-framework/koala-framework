<?php
class Vps_Update_31658 extends Vps_Update
{
    protected $_tags = array('vps');

    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
            'file' => 'benchmark.rrd',
            'name' => 'memcache-bytes',
            'type' => 'GAUGE',
            'max' => pow(2, 64),
        ));
        $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
            'file' => 'benchmark.rrd',
            'name' => 'memcache-curr-items',
            'type' => 'GAUGE',
            'max' => pow(2, 64),
        ));
        $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
            'file' => 'benchmark.rrd',
            'name' => 'memcache-curr-connections',
            'type' => 'GAUGE',
            'max' => pow(2, 64),
        ));
    }
}
