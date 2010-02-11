<?php
class Vps_Component_NoCategoriesRoot extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vpc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'paragraphs' => 'Vpc_Paragraphs_Component',
                'link' => 'Vpc_Basic_LinkTag_Component',
                'firstChildPage' => 'Vpc_Basic_LinkTag_FirstChildPage_Component'
            ),
            'model' => 'Vpc_Root_Category_GeneratorModel'
        );
        unset($ret['generators']['category']);
        return $ret;
    }
}
