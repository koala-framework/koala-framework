<?php
class Vps_Update_30932 extends Vps_Update
{
    protected $_tags = array('vps');

    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
            'file' => 'benchmark.rrd',
            'name' => 'mysql-processes',
            'type' => 'GAUGE',
            'max' => 1000,
        ));
        $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
            'file' => 'benchmark.rrd',
            'name' => 'mysql-processes-select',
            'type' => 'GAUGE',
            'max' => 1000,
        ));
        $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
            'file' => 'benchmark.rrd',
            'name' => 'mysql-processes-modify',
            'type' => 'GAUGE',
            'max' => 1000,
        ));
        $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
            'file' => 'benchmark.rrd',
            'name' => 'mysql-processes-others',
            'type' => 'GAUGE',
            'max' => 1000,
        ));
        $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
            'file' => 'benchmark.rrd',
            'name' => 'mysql-processes-locked',
            'type' => 'GAUGE',
            'max' => 1000,
        ));
    }
}
