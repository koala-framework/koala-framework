<?php
class Vps_Component_Output_Child extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'component' => 'Vps_Component_Output_ChildChild',
            'class' => 'Vps_Component_Generator_Static'
        );
        return $ret;
    }
    
    public function hasContent()
    {
        return true;
    }
}
?>