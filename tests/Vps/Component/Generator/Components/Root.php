<?php
class Vps_Component_Generator_Components_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multiple', 'is_home'=>true, 'category' =>'main'),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Bar', 'filename' => 'bar',
                  'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'category' =>'main')
        )));
        $ret['generators']['page']['component'] = array('multiple' => 'Vps_Component_Generator_Components_Multiple', 'empty' => 'Vpc_Basic_Empty_Component');
        $ret['generators']['box']['component'] = array('empty' => 'Vpc_Basic_Empty_Component');
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_Components_Multiple'
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
?>