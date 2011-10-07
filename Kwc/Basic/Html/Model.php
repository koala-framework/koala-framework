<?php
class Kwc_Basic_Html_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_basic_html';

    public function __construct($config = array())
    {
        if (!isset($this->_default['content'])) {
            $this->_default['content'] = '<p>'.Kwc_Abstract::LOREM_IPSUM.'</p>';
        }
        parent::__construct($config);
    }
}
