<?php
class Vps_Component_Output_Link_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['c1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Output_C1_ChildChild_Component'
        );
        return $ret;
    }
}
?>