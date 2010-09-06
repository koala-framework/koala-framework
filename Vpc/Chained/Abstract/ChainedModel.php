<?php
class Vpc_Chained_Abstract_ChainedModel extends Vps_Model_FnF
{
    protected $_columns = array('id', 'filename', 'name');
    protected $_primaryKey = 'id';
    protected $_toStringField = 'name';

    public function __construct(array $values = array())
    {
        $config['data'] = array();
        foreach ($values as $key => $value) {
            $config['data'][] = array(
                'id' => $key,
                'filename' => $key,
                'name' => $value,
                'visible' => 1
            );
        }
        $config['toStringField'] = 'name';
        parent::__construct($config);
    }
}
