<?php
class Kwf_Data_Table_FileUrl extends Kwf_Data_Abstract
{
    protected $_type;
    protected $_rule;

    public function __construct($rule, $type = 'default')
    {
        $this->_rule = $rule;
        $this->_type = $type;
    }

    public function load($row)
    {
        return Kwf_Media::getUrl(get_class($row->getModel()), $row->id, $this->_type, $row->getParentRow($this->_rule));
    }
}
