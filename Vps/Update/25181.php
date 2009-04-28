<?php
class Vps_Update_25181 extends Vps_Update
{
    protected function _init()
    {
        $fields = array('content-rendered partial cache',
            'content-rendered partial nocache',
            'content-rendered partial noviewcache');
        foreach ($fields as $f) {
            $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
                'file' => 'benchmark.rrd',
                'name' => Vps_Util_Rrd_Field::escapeField($f),
                'max' => 256,
                'backup' => false
            ));
        }
    }
    public function preUpdate()
    {
        copy('benchmark.rrd', 'benchmark-backup'.time().'.rrd');
        parent::preUpdate();
    }
}
