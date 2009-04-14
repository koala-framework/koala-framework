<?php
class Vps_Update_26245 extends Vps_Update
{
    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Rrd_RenameDs(array(
            'file' => 'benchmark.rrd',
            'name' => Vps_Controller_Action_Cli_BenchmarkController::escapeField('content-rendered partial noviewcache'),
            'newName' => Vps_Controller_Action_Cli_BenchmarkController::escapeField('content-rendered partial noviewc'),
        ));
    }
}
