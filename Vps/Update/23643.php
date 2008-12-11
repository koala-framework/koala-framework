<?php
class Vps_Update_23643 extends Vps_Update
{
    protected function _init()
    {
        foreach (array('content', 'media', 'admin', 'asset', 'cli', 'unkown') as $t) {
            $this->_actions[] = new Vps_Update_Action_Rrd_RenameDs(array(
                'file' => 'benchmark.rrd',
                'name' => Vps_Controller_Action_Cli_BenchmarkController::escapeField($t.'-rendered cache (preloaded)'),
                'newName' => Vps_Controller_Action_Cli_BenchmarkController::escapeField($t.'-rendered cache'),
            ));
            $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
                'file' => 'benchmark.rrd',
                'name' => Vps_Controller_Action_Cli_BenchmarkController::escapeField($t.'-rendered noviewcache'),
                'max'=>(2^31)
            ));
        }
    }
}
