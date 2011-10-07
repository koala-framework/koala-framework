<?php
class Vpc_Trl_Posts_Posts_Model extends Vpc_Posts_Directory_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnFFile(array(
            'uniqueIdentifier' => 'Vpc_Trl_Posts_Posts_Model',
            'data' => array()
        ));
        parent::__construct($config);
    }
}
