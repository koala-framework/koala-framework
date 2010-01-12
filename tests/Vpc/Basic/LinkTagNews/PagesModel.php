<?php
class Vpc_Basic_LinkTagNews_PagesModel extends Vps_Component_PagesModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(

                // necessary news component
                array('id'=>2100, 'pos'=>1, 'visible'=>true, 'name'=>'NewsBar', 'filename' => 'newsbar1',
                    'parent_id'=>'root', 'component'=>'news', 'is_home'=>false, 'hide'=>false),

                // real link
                array('id'=>5100, 'pos'=>2, 'visible'=>true, 'name'=>'NewsLink 1', 'filename' => 'newslink1',
                        'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false),

                // dummy
                array('id'=>5200, 'pos'=>3, 'visible'=>true, 'name'=>'NewsLink 2', 'filename' => 'newslink2',
                        'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false),

            )
        ));
        parent::__construct($config);
    }
}
