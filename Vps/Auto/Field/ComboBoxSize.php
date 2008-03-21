<?php
class Vps_Auto_Field_ComboBoxSize extends Vps_Auto_Field_ComboBox
{
    public function setSizes($sizes)
    {
        $data = array();
        foreach ($sizes as $key => $val) {
            if (is_null($val)) {
                $str = 'Original Size';
                $data[] = array(null, $str);
            } else {
                $str = $val[0] . ' x ' . $val[1];
                $data[] = array($str, $str);
            }
        }
        $this->setForceSelection(true)
            ->setStore(array('data' => $data))
            ->setTriggerAction('all')
            ->setEditable(false);
    }

    public function load($row)
    {
        $store = $this->getStore();
        $value = '';
        foreach ($store['data'] as $key => $val) {
            if ($val[0] == $row->width . ' x ' . $row->height) {
                $value = $val[0];
            }
        }
        return array($this->getFieldName() => $value);
    }

    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        Vps_Auto_Field_Abstract::prepareSave($row, $postData);
        $value = $postData[$this->getFieldName()];
        if ($value == 'null') {
            $row->width = null;
            $row->height = null;
        } else {
            $values = explode('x', $value);
            $row->width = (int)$values[0];
            $row->height = (int)$values[1];
        }

        $this->load($row);
    }
}
