<?php
class Vpc_Root_CategoryModel extends Vps_Model_Data_Abstract
{
    private $_pageCategories;
    protected $_columns = array('id', 'name', 'component');

    public function __construct($config = array())
    {
        if (isset($config['pageCategories'])) {
            $this->_pageCategories = $config['pageCategories'];
        } else {
            $this->_pageCategories = Vps_Registry::get('config')->vpc->pageCategories;
        }
        parent::__construct($config);
    }

    protected function _init()
    {
        $this->_data = array();
        if ($this->_pageCategories) {
            foreach ($this->_pageCategories as $key => $val) {
                $this->_data[] = array('id' => $key, 'name' => $val, 'component'=>$key);
            }
        }
        parent::_init();
    }
}
