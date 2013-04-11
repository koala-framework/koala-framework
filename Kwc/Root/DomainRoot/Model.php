<?php
class Kwc_Root_DomainRoot_Model extends Kwf_Model_Data_Abstract
{
    private $_domains;
    protected $_columns = array('id', 'name', 'domain', 'component', 'pattern');
    protected $_toStringField = 'name';

    public function __construct($config = array())
    {
        if (isset($config['domains'])) {
            $this->_domains = $config['domains'];
        } else {
            $this->_domains = Kwf_Registry::get('config')->kwc->domains->toArray();
        }
        parent::__construct($config);
    }

    protected function _init()
    {
        $this->_data = array();
        foreach ($this->_domains as $key => $val) {
            $pattern = isset($val['pattern']) ? $val['pattern'] : null;
            $this->_data[] = array(
                'id' => $key,
                'name' => isset($val['name']) ? $val['name'] : $key,
                'domain' => $val['domain'],
                'component' => $key,
                'pattern' => $pattern
            );
        }
        parent::_init();
    }

    public function getRowByHost($host)
    {
        $rows = $this->getRows();
        foreach ($rows as $row) {
            if ($row->domain == $host) {
                return $row;
            }
        }
        $ret = null;
        //TODO: this always picks the *first* domain that's not always wanted
        foreach ($rows as $row) {
            if (!$ret && !$row->pattern) $ret = $row;
            if ($row->pattern && preg_match('/' . $row->pattern . '/', $host)
            ) {
                $ret = $row;
            }
        }
        return $ret;
    }
}
