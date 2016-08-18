<?php
class Kwf_Component_Output_C1_Root_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = array(
            'child' => 'Kwf_Component_Output_C1_Child_Component'
        );
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwf_Component_Output_C1_Box_Component',
            'inherit' => true,
            'priority' => 0
        );
        $ret['contentWidth'] = 600;
        return $ret;
    }
}
?>