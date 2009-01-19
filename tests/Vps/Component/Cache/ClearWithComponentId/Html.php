<?php
class Vps_Component_Cache_ClearWithComponentId_Html extends Vpc_Basic_Html_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['modelname']);
        static $m;
        if (!isset($m)) {
            $m = new Vps_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data' => array(
                    array('component_id' => 'root-child', 'content' => 'foo')
                )
            ));
        }
        $ret['model'] = $m;
        return $ret;
    }

}
