<?php
class Vps_Update_23643 extends Vps_Update
{
    protected function _init()
    {
        foreach (array('content', 'media', 'admin', 'asset', 'cli', 'unkown') as $t) {
            $this->_actions[] = new Vps_Update_Action_Rrd_RenameDs(array(
                'file' => 'benchmark.rrd',
                'name' => Vps_Util_Rrd_Field::escapeField($t.'-rendered cache (preloaded)'),
                'newName' => Vps_Util_Rrd_Field::escapeField($t.'-rendered cache'),
            ));
        }
    }
}
