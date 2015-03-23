<?php
class Kwc_Trl_PagesPlusTable_TestTableComponent_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Page_Table',
            'component' => 'Kwc_Trl_PagesPlusTable_TestTableComponent_Child_Component',
        );
        $ret['childModel'] = 'Kwc_Trl_PagesPlusTable_TestTableComponent_ChildModel';
        return $ret;
    }
}
