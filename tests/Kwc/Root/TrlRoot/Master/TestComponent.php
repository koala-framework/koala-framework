<?php
class Vpc_Root_TrlRoot_Master_TestComponent extends Vpc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['category']);
        $ret['generators']['page'] = array(
            'class' => 'Vpc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'empty' => 'Vpc_Basic_Empty_Component',
                'static' => 'Vpc_Root_TrlRoot_Master_Static_Component'
            ),
            'model' => 'Vpc_Root_TrlRoot_Master_PagesTestModel'
        );
        return $ret;
    }
}
