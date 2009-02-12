<?php
class Vps_Component_CacheVars_Paragraphs_Paragraphs extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs']['model'] = 'Vps_Component_CacheVars_Paragraphs_Model';
        $ret['generators']['paragraphs']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component',
            'custom' => 'Vps_Component_CacheVars_Paragraphs_Component'
        );
        unset($ret['tablename']);
        return $ret;
    }
}
