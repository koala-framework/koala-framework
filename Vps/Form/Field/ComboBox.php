<?php
class Vps_Form_Field_ComboBox extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('combobox');
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        $store = $this->getStore();
        if (isset($store['data'])) {
            $a = array('');
            foreach ($store['data'] as $r) {
                $a[] = $r[0];
            }
            $this->addValidator(new Zend_Validate_InArray($a));
        } else if (isset($store['url'])) {
            //todo, keine ahnung wie :D
        }

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
            if (isset($data['data'])) $data = $data['data'];
            $d = array();
            foreach ($data as $k=>$i) {
                if (!is_array($i)) {
                    $d[] = array($k, $i);
                } else {
                    if (isset($i['id'])) $id = $i['id'];
                    elseif (isset($i[0])) $id = $i[0];
                    else throw new Vps_Exception("id not found");
                    if (isset($i['value'])) $value = $i['value'];
                    else if (isset($i[1])) $value = $i[1];
                    else throw new Vps_Exception("value not found");
                    $d[] = array($id, $value);
                }
            }
            return $this->setStore(array('data' => $d));
        }
    }

    public function setFields(array $fields)
    {
        if (!in_array('id', $fields) || !in_array('name', $fields)) {
            throw new Vps_Exception('fields \'id\' and \'name\' must be set when using setFields method');
        }

        $store = $this->getStore();
        $store['fields'] = $fields;
        return $this->setStore($store);
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == '' || $ret == 'null') $ret = null;
        return $ret;
    }
}
