<?php
class Kwc_Trl_Posts_Posts_Model extends Kwc_Posts_Directory_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnFFile(array(
            'uniqueIdentifier' => 'Kwc_Trl_Posts_Posts_Model',
            'data' => array()
        ));
        parent::__construct($config);
    }
}
