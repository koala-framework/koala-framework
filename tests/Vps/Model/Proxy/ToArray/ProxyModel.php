<?php
class Vps_Model_Proxy_ToArray_ProxyModel extends Vps_Model_Proxy
{
    protected $_siblingModels = array('Vps_Model_Proxy_ToArray_SiblingModel');

    protected function _init()
    {
        $this->_proxyModel = new Vps_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname', 'timefield'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'mch', 'timefield' => '1234')
            )
        ));
        parent::_init();
    }
}
