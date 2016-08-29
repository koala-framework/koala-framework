<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Root_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_Component',
        );
        return $ret;
    }
}
