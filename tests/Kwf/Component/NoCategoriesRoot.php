<?php
class Kwf_Component_NoCategoriesRoot extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'paragraphs' => 'Kwc_Paragraphs_Component',
                'link' => 'Kwc_Basic_LinkTag_Component',
                'firstChildPage' => 'Kwc_Basic_LinkTag_FirstChildPage_Component'
            ),
            'model' => 'Kwc_Root_Category_GeneratorModel'
        );
        unset($ret['generators']['category']);
        return $ret;
    }
}
