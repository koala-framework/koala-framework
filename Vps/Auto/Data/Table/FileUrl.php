<?php
class Vps_Auto_Data_Table_FileUrl extends Vps_Auto_Data_Abstract
{
    protected $_rule;
    protected $_type;
    protected $_filename;
    protected $_addRandom;

    public function __construct($rule = null, $type = 'default', $filename = null, $addRandom = false)
    {
        $this->_rule = $rule;
        $this->_type = $type;
        $this->_filename = $filename;
        $this->_addRandom = $addRandom;
    }

    public function load($row)
    {
        return $row->getFileUrl($this->_rule, $this->_type,
                                $this->_filename, $this->_addRandom);
    }
}
