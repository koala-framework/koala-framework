<?php
class Kwc_Directories_List_ViewAjax_ViewModelRow extends Kwf_Model_Row_Abstract
{
    protected $_data;

    public function __construct(array $config)
    {
        $this->_data = $config['data'];
        parent::__construct($config);
    }

    public function __isset($name)
    {
        return isset($this->_data->$name);
    }

    public function __get($name)
    {
        return $this->_data->$name;
    }
}
