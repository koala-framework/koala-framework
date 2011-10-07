<?php
class Vps_Component_Acl_AllowedComponents_PagesModel extends Vps_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Special', 'filename' => 'special',
                  'parent_id'=>'root', 'component'=>'specialContainer', 'is_home'=>true, 'hide'=>false),

            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Pages', 'filename' => 'pages',
                  'parent_id'=>'root', 'component'=>'pagesContainer', 'is_home'=>false, 'hide'=>false),
    );
}
