<?php
class Kwf_Component_Cache_MenuDeviceVisible_Root_Model extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config = array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'f1', 'filename' => 'f1', 'parent_subroot_id'=>'root',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'device_visible' => 'hideOnMobile', 'hide'=>false, 'custom_filename' => null),
            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'f2', 'filename' => 'f2', 'parent_subroot_id'=>'root',
                  'parent_id'=>'1', 'component'=>'empty', 'is_home'=>false, 'device_visible' => 'all', 'hide'=>false, 'custom_filename' => null),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'f3', 'filename' => 'f3', 'parent_subroot_id'=>'root',
                  'parent_id'=>'2', 'component'=>'empty', 'is_home'=>false, 'device_visible' => 'all', 'hide'=>false, 'custom_filename' => null),
            array('id'=>4, 'pos'=>2, 'visible'=>true, 'name'=>'f4', 'filename' => 'f4', 'parent_subroot_id'=>'root',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'device_visible' => 'all', 'hide'=>false, 'custom_filename' => null),
            array('id'=>5, 'pos'=>3, 'visible'=>false, 'name'=>'f5', 'filename' => 'f5', 'parent_subroot_id'=>'root',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'device_visible' => 'all', 'hide'=>false, 'custom_filename' => null),
        ));
        $config = array('proxyModel' => new Kwf_Model_FnF($config));
        parent::__construct($config);
    }

    protected function _setupFilters()
    {
    }
}
