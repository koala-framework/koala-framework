<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_Foo_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_Foo_FooChild_Component'
        );
        return $ret;
    }
}
