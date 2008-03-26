<?php
class Vps_Auto_Field_ComboBoxText extends Vps_Auto_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('combobox');
    }

    protected function _addValidators()
    {
        Vps_Auto_Field_SimpleAbstract::_addValidators();
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        if (isset($ret[0]['storeUrl'])) {
            $ret[0]['store'] = array('url' => $ret[0]['storeUrl']);
        }
        return $ret;
    }

    public function setValues($data)
    {
        if (is_string($data)) {
            return $this->setStore(array('url' => $data));
        } else if ($data instanceof Vps_Db_Table_Rowset) {
            $data = $data->toStringDataArray();
            return $this->setStore(array('data' => $data));
        } else if (is_array($data)) {
            $d = array();
            foreach ($data as $k=>$i) {
                if (!is_array($i)) {
                    $d[] = array($k, $i);
                } else {
                    $d[] = $i;
                }
            }
            return $this->setStore(array('data' => $d));
        }
    }
    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == '' || $ret == 'null') $ret = null;
        return $ret;
    }
}
