<?php
class Vps_Component_Output_Child extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = array(
            'child' => 'Vps_Component_Output_ChildChild'
        );
        $ret['plugins'] = array('Vps_Component_Output_Plugin', 'Vps_Component_Output_Plugin');
        return $ret;
    }
    
    public function getTemplateFile()
    {
        return dirname(__FILE__) . '/Child.tpl';
    }
    
    public function hasContent()
    {
        return true;
    }
}
?>