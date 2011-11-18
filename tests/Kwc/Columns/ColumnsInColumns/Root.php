<?php
class Kwc_Columns_ColumnsInColumns_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'columns', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Foo2', 'filename' => 'foo2',
                  'parent_id'=>'root', 'component'=>'columns', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>3, 'pos'=>3, 'visible'=>true, 'name'=>'Foo3', 'filename' => 'foo3',
                  'parent_id'=>'root', 'component'=>'columns', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'columns' => 'Kwc_Columns_ColumnsInColumns_Paragraphs_Component',
        );

        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Columns_ColumnsInColumns_Box_Component',
            'inherit' => true,
        );
        $ret['generators']['uniqueBox'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Columns_ColumnsInColumns_Box_Component',
            'inherit' => true,
            'unique' => true
        );

        $ret['contentWidthBoxSubtract'] = array(
            'box' => 100,
            'uniqueBox' => 50,
        );

        unset($ret['generators']['title']);
        return $ret;
    }
}
