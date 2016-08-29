<?php
class Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Child_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Child_Page_Component'
        );
        return $ret;
    }
}
