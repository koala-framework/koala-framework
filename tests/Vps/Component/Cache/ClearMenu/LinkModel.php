<?php
class Vps_Component_Cache_ClearMenu_LinkModel extends Vpc_Basic_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'1', 'component'=>'intern')
            )
        ));
        parent::__construct($config);
    }
}
