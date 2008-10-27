<?php
class Vps_Component_Output_ChildNoCache extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }
    
    public function getTemplateFile()
    {
        return dirname(__FILE__) . '/ChildNoCache.tpl';
    }
}
?>