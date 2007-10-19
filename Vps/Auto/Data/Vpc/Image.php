<?php
class Vps_Auto_Data_Vpc_Image extends Vps_Auto_Data_Abstract
{
    protected $_component;

    public function __construct($component)
    {
        $this->_component = $component;
    }

    public function load($row)
    {
        foreach ($this->_component->getChildComponents() as $c) {
            if ($c->getCurrentComponentKey() == '-' . $row->id) {
                $url = $c->getImageUrl(Vpc_Basic_Image_Index::SIZE_MINI, true);
            }
        }
        if (isset($url)) {
            return '<img src="' . $url . '" />';
        } else {
            return '';
        }
    }

    public function save(Zend_Db_Table_Row_Abstract $row, $data)
    {
        throw new Vps_Exception('Save is not possible for Vps_Auto_Data_Table_Parent.');
    }

    public function delete()
    {
        throw new Vps_Exception('Delete is not possible for Vps_Auto_Data_Table_Parent.');
    }
}