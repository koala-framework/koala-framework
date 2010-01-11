<?php
class Vps_Component_NoCategoriesRoot extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_Page',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'paragraphs' => 'Vpc_Paragraphs_Component',
                'link' => 'Vpc_Basic_LinkTag_Component',
                'firstChildPage' => 'Vpc_Basic_LinkTag_FirstChildPage_Component'
            ),
            'table' => 'Vps_Component_PagesModel'
        );
        unset($ret['generators']['category']);
        return $ret;
    }
}
