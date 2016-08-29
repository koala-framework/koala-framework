<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_CreatesFooPage_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['fooPage'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_Foo_Component'
        );
        return $ret;
    }
}
