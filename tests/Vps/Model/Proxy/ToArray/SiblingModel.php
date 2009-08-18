<?php
class Vps_Model_Proxy_ToArray_SiblingModel extends Vps_Model_FnF
{

    public function __construct(array $config = array())
    {
        $config['uniqueIdentifier'] = 'unique';
        $config['columns'] = array('id', 'sib_lastname');
        $config['uniqueColumns'] = array('id');
        $config['data'] = array(
            array('id' => 1, 'sib_lastname' => 'herbertsen')
        );
        parent::__construct($config);
    }

    protected $_referenceMap = array(
        'proxy' => array(
            'column' => 'id',
            'refModelClass' => 'Vps_Model_Proxy_ToArray_ProxyModel'
        )
    );
}
