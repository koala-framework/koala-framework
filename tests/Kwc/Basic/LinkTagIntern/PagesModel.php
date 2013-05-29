<?php
class Kwc_Basic_LinkTagIntern_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                //normaler, gültiger link
                array('id'=>1300, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo1', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

                //keine seite ausgewählt
                array('id'=>1301, 'pos'=>2, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo2', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

                //seite ausgewählt, aber nicht vorhanden
                array('id'=>1302, 'pos'=>3, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo3', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

                //seite ausgewählt, aber unsichtbar
                array('id'=>1303, 'pos'=>4, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo3', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),


                //target für 1300
                array('id'=>1310, 'pos'=>5, 'visible'=>true, 'name'=>'Bar', 'filename' => 'bar', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

                //target für 1303 (unsichtbar)
                array('id'=>1311, 'pos'=>6, 'visible'=>false, 'name'=>'Bar2', 'filename' => 'bar2', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root')
            )
        ));
        parent::__construct($config);
    }
}
