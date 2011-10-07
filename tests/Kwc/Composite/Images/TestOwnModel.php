<?php
class Kwc_Composite_Images_TestOwnModel extends Kwc_Abstract_List_OwnModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array('component_id', 'visible'),
                'data'=> array()
            ));
        parent::__construct($config);
    }
}
