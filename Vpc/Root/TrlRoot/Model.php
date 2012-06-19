<?php
class Vpc_Root_TrlRoot_Model extends Vps_Model_FnF
{
    protected $_columns = array('id', 'filename', 'name', 'master');
    protected $_primaryKey = 'id';
    protected $_toStringField = 'name';
    protected $_siblingModels = array('Vpc_Root_TrlRoot_SiblingModel');

    public function __construct(array $values = array())
    {
        $config['data'] = array();
        $master = true;
        foreach ($values as $key => $value) {
            $config['data'][] = array(
                'id' => $key,
                'filename' => $key,
                'name' => $value,
                'master' => $master
            );
            $master = false;
        }
        parent::__construct($config);
    }
}
