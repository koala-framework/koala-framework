<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => array(
                'foo' => 'Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_Foo_Component',
                'createsFooPage' => 'Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_CreatesFooPage_Component'
            ),
            'model' => 'Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_Model'
        );
        return $ret;
    }
}
