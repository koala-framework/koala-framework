<?php
class Vps_Component_Generator_UniqueLevel_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'category' =>'main', 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>2, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component'
        );

        unset($ret['generators']['title']);
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(
                'menu' => 'Vps_Component_Generator_UniqueLevel_Test_Component'
            ),
            'inherit' => true,
            'uniqueContentLevel' => 3,
            'priority' => 0
        );
        return $ret;
    }
}
