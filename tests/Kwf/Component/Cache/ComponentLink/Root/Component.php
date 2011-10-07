<?php
class Vps_Component_Cache_ComponentLink_Root_Component extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vps_Component_Cache_ComponentLink_Root_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component',
        );
        unset($ret['generators']['title']);
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        for ($x = 1; $x <= 4; $x++) {
            $ret["p$x"] = Vps_Component_Data_Root::getInstance()->getComponentById($x);
        }
        return $ret;
    }
}
