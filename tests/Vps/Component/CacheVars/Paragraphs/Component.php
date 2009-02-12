<?php
class Vps_Component_CacheVars_Paragraphs_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['model'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id' => 'root-paragraphs-2')
            )
        ));
        return $ret;
    }
}
