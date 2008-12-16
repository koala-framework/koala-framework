<?php
class Vps_Component_Cache_ClearMenu_RootModel extends Vps_Model_FnF
{
    public function __construct($config = array())
    {
        $config['data'] = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'testlink', 'filename' => 'testlink',
                  'parent_id'=>null, 'component'=>'link', 'is_home'=>false, 'category' =>null)
        );
        parent::__construct($config);
    }
}
