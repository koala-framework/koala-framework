<?php
class Vps_Component_Generator_StaticSelect_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        //$ret['generators']['page']['model'] = new Vps_Model_FnF();
        //$ret['generators']['page']['component'] = array();

        $ret['generators']['page1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'page1'
        );
        $ret['generators']['page2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'page2'
        );
        $ret['generators']['page3'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'page3'
        );

        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_StaticSelect',
            'component' => array(
                'empty' => 'Vpc_Basic_Empty_Component',
                'banner' => 'Vps_Component_Generator_StaticSelect_Banner_Component',
            ),
            'inherit' => true,
            'model' => new Vps_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data' => array(
                    array('component_id' => 'root_page1-box', 'component' => 'empty'),
                    array('component_id' => 'root_page2-box', 'component' => 'banner'),
                )
            ))
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
