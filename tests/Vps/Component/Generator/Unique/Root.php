<?php
class Vps_Component_Generator_Unique_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>'root', 'component'=>'page1', 'is_home'=>true, 'category' =>'main', 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>1, 'component'=>'page2', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'page1' => 'Vps_Component_Generator_Unique_TablePage1',
            'page2' => 'Vps_Component_Generator_Unique_TablePage2'
        );

        $ret['generators']['box']['priority'] = 1;
        $ret['generators']['box']['component'] = array();
        $ret['generators']['box']['component']['box'] = 'Vps_Component_Generator_Unique_Box';
        unset($ret['generators']['title']);
        $ret['generators']['page2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_Unique_Page2',
            'name' => 'page2'
        );
        return $ret;
    }
}
