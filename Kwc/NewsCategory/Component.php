<?php
class Kwc_NewsCategory_Component extends Kwc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_NewsCategory_Model';
        $ret['generators']['child']['component']['view'] = 'Kwc_NewsCategory_View_Component';
        $ret['generators']['detail']['component'] = 'Kwc_NewsCategory_Detail_Component';
        $ret['generators']['categories'] = array(
            'class' => 'Kwf_Component_Generator_PseudoPage_Static',
            'component' => 'Kwc_NewsCategory_Category_Directory_Component',
            'name' => trlKwfStatic('Categories'),
            'showInMenu' => false
        );
        return $ret;
    }
}
