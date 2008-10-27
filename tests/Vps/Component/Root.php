<?php
class Vps_Component_Output_Root extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = array(
            'child' => 'Vps_Component_Output_Child'
        );
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vps_Component_Output_Box',
            'inherit' => true,
            'priority' => 0
        );
        
        return $ret;
    }
    
    public function getTemplateFile()
    {
        return dirname(__FILE__) . '/Root.tpl';
    }
}
?>