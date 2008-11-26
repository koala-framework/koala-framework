<?php
class Vpc_Root_DomainRoot_Model extends Vps_Model_Data_Abstract
{
    private $_domains;
    protected $_columns = array('id', 'name', 'domain');

    public function __construct($config = array())
    {
        if (isset($config['domains'])) {
            $this->_domains = $config['domains'];
        } else {
            $this->_domains = Vps_Registry::get('config')->vpc->domains->toArray();
        }
        parent::__construct($config);
    }

    protected function _init()
    {
        $this->_data = array();
        foreach ($this->_domains as $key => $val) {
            $this->_data[] = array('id' => $key, 'name' => $val['name'], 'domain' => $val['domain']);
        }
        parent::_init();
    }
}
