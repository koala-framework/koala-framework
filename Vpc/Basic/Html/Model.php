<?php
class Vpc_Basic_Html_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_html';

    public function __construct($config = array())
    {
        if (!isset($this->_default['content'])) {
            $this->_default['content'] = '<p>'.Vpc_Abstract::LOREM_IPSUM.'</p>';
        }
        parent::__construct($config);
    }
}
