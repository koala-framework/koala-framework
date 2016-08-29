<?php
class Kwf_Component_Output_Link_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['c1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Output_C1_ChildChild_Component'
        );
        return $ret;
    }
}
?>