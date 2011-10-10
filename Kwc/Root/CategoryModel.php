<?php
class Kwc_Root_CategoryModel extends Kwf_Model_Data_Abstract
{
    private $_pageCategories;
    protected $_columns = array('id', 'name', 'component');

    public function __construct($config = array())
    {
        if (isset($config['pageCategories'])) {
            $this->_pageCategories = $config['pageCategories'];
        } else {
            $this->_pageCategories = Kwf_Config::getValueArray('kwc.pageCategories');
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

    public function isEqual(Kwf_Model_Interface $other)
    {
        return $other === $this;
    }
}
