<?php
class Vpc_Composite_Images_TestOwnModel extends Vpc_Abstract_List_OwnModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('component_id', 'visible'),
                'data'=> array()
            ));
        parent::__construct($config);
    }
}
