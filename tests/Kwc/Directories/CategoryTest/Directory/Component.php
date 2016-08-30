<?php
class Kwc_Directories_CategoryTest_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Directories_CategoryTest_Directory_Model';
        $ret['generators']['detail']['component'] = 'Kwc_Directories_CategoryTest_Detail_Component';
        $ret['generators']['categories'] = array(
            'class' => 'Kwf_Component_Generator_PseudoPage_Static',
            'component' => 'Kwc_Directories_CategoryTest_Category_Directory_Component',
            'name' => 'categories',
        );
        return $ret;
    }
}
