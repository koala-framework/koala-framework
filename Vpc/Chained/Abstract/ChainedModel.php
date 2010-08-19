<?php
class Vpc_Chained_Abstract_ChainedModel extends Vps_Model_FnF
{
    protected $_columns = array('component_id', 'filename', 'name', 'master');
    protected $_primaryKey = 'component_id';

    public function __construct(array $values = array())
    {
        $config['data'] = array();
        $master = true;
        foreach ($values as $key => $value) {
            $config['data'][] = array(
                'component_id' => 'root-' . $key,
                'filename' => $key,
                'name' => $value,
                'master' => $master,
                'visible' => 1
            );
            $master = false;
        }
        $config['toStringField'] = 'name';
        parent::__construct($config);
    }
}
