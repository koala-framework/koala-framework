<?php
class Kwf_Component_Acl_AllowedComponents_PagesModel extends Kwf_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Special', 'filename' => 'special', 'custom_filename' => true,
                  'parent_id'=>'root', 'component'=>'specialContainer', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root'),

            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Pages', 'filename' => 'pages', 'custom_filename' => true,
                  'parent_id'=>'root', 'component'=>'pagesContainer', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
    );
}
