<?php
class Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Table_Component'
        );
        $ret['generators']['cards'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Cards_Component'
        );
        return $ret;
    }
}
