<?php
class Kwf_Component_Output_C3_ChildPage_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['childpage'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Output_C3_ChildPage2_Component'
        );
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwf_Component_Output_C3_Box_Component',
            'inherit' => true,
            'priority' => 0
        );
        $ret['contentWidth'] = 600;
        return $ret;
    }
}
?>