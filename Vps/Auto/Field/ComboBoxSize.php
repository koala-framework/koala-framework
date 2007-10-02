<?php
class Vps_Auto_Field_ComboBoxSize extends Vps_Auto_Field_ComboBox
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct('size', $field_label);
    }

    public function setSizes($sizes)
    {
        $data = array();
        if (is_array($sizes[0])) {
            foreach ($sizes as $key => $val) {
                $str = $val[0] . ' x ' . $val[1];
                $data[] = array($str, $str);
            }
        } else {
            $str = $sizes[0] . ' x ' . $sizes[1];
            $data[] = array($str, $str);
        }
        $this->setForceSelection(true)
            ->setStore(array('data' => $data))
            ->setTriggerAction('all')
            ->setEditable(false);
    }
    
    public function getMetaData()
    {
        $ret = parent::getMetaData();
        $ret['type'] = 'ComboBox';
        return $ret;
    }

    public function load($row)
    {
        return array();
    }

    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        Vps_Auto_Field_Abstract::prepareSave($row, $postData);

        $values = explode('x', $postData['size']);
        $row->width = (int)$values[0];
        $row->height = (int)$values[1];

        $this->load($row);
    }
}
