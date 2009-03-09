<?php
class Vps_Update_25181 extends Vps_Update
{
    protected function _init()
    {
        /*
        $fields = array(
            'duration', 'queries',
            'componentDatas', 'generators', 'componentData Pages', 'components',
            'preload cache', 'rendered nocache', 'rendered cache',
            'getRecursiveChildComponents', 'getChildComponents uncached', 'getChildComponents cached', 'countChildComponents',
            'iccc cache semi-hit', 'iccc cache miss', 'iccc cache hit',
            'Generator::getInst semi-hit', 'Generator::getInst miss', 'Generator::getInst hit',
            'processing dependencies miss', 'rendered noviewcache'
        );
        $del= array();
        foreach (array('media', 'admin', 'asset', 'cli', 'unkown') as $t) {
            foreach ($fields as $f) {
                if ($f == 'duration' && $t == 'admin') continue;
                if ($f == 'queries' && $t == 'admin') continue;
                $del[] = $t.'-'.$f;
            }
        }
        $del[] = 'content-iccc cache semi-hit';
        $del[] = 'content-iccc cache miss';
        $del[] =  'content-iccc cache hit';
        foreach ($del as &$i) {
            $i = Vps_Controller_Action_Cli_BenchmarkController::escapeField($i);
            $this->_actions[] = new Vps_Update_Action_Rrd_DropDs(array(
                'file' => 'benchmark.rrd',
                'name' => $i,
                'backup' => false
            ));
        }
        */
        $fields = array('content-rendered partial cache',
            'content-rendered partial nocache',
            'content-rendered partial noviewcache');
        foreach ($fields as $f) {
            $this->_actions[] = new Vps_Update_Action_Rrd_AddDs(array(
                'file' => 'benchmark.rrd',
                'name' => Vps_Controller_Action_Cli_BenchmarkController::escapeField($f),
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
