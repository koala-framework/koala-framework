<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_Foo_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_Foo_FooChild_Component'
        );
        return $ret;
    }
}
