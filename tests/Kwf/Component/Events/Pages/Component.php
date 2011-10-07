<?php
class Vps_Component_Events_Pages_Component extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vps_Component_Events_Pages_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component',
            'text' => 'Vpc_Basic_Text_Component',
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
?>