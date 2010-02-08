<?php
class Vpc_Basic_Image_Cache_Root_ListModel extends Vpc_Abstract_List_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'id',
                'data'=> array(
//                     array('id' => 1, 'component_id'=>'root', 'pos'=>1, 'visible' => 1),
                )
            ));
        parent::__construct($config);
    }
}
