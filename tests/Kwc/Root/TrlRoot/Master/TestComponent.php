<?php
class Kwc_Root_TrlRoot_Master_TestComponent extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['category']);
        $ret['generators']['page'] = array(
            'class' => 'Kwc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'empty' => 'Kwc_Basic_None_Component',
                'static' => 'Kwc_Root_TrlRoot_Master_Static_Component'
            ),
            'model' => 'Kwc_Root_TrlRoot_Master_PagesTestModel'
        );
        return $ret;
    }
}
