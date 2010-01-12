<?php
class Vps_Update_31736 extends Vps_Update
{
    protected $_tags = array('vps');

    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
            'file' => 'benchmark.rrd',
            'name' => 'memcache-limit-maxbytes',
            'type' => 'GAUGE',
            'max' => pow(2, 64),
        ));
    }
}
