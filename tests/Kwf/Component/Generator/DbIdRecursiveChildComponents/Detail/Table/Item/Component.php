<?php
class Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Table_Item_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Table_Item_Page_Component'
        );
        return $ret;
    }
}
