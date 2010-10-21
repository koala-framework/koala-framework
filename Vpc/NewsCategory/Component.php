<?php
class Vpc_NewsCategory_Component extends Vpc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_NewsCategory_Model';
        $ret['generators']['child']['component']['view'] = 'Vpc_NewsCategory_View_Component';
        $ret['generators']['detail']['component'] = 'Vpc_NewsCategory_Detail_Component';
        $ret['generators']['categories'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Static',
            'component' => 'Vpc_NewsCategory_Category_Directory_Component',
            'name' => trlVps('Categories'),
            'showInMenu' => false
        );
        return $ret;
    }
}
