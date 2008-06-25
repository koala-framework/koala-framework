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

    //setValues
        //url (string), rowset, array
    
    //setFields
        //array mit feldern

    //setTpl
        //string mit template, standardwert in ext definiert:
        //'<tpl for="."><div class="x-combo-list-item">{' + this.displayField + '}</div></tpl>';

    //setShowNoSelection
        //keine auswahl anbieten
        

    public function getMetaData()
    {
        $ret = parent::getMetaData();


        $ret['store'] = $this->_getStoreData();
        if (isset($ret['fields'])) unset($ret['fields']);
        if (isset($ret['values'])) unset($ret['values']);

        return $ret;
    }
    
    protected function _getStoreData()
    {
        $store = $this->getStore();
        if (!$store) $store = array();

        $fields = $this->getFields();
        if ($fields) {
            $store['fields'] = $fields;
        }

        if ($this->getStoreUrl()) {
            $store['url'] = $this->getStoreUrl();
        }
        $data = $this->getValues();
        if (is_string($data)) {
            $store['url'] = $data;
        } else if ($data instanceof Vps_Db_Table_Rowset_Abstract) {
            if ($this->getFields()) {
                $store['data'] = $data->toStringDataArray($fields);
            } else {
                $store['data'] = $data->toStringDataArray();
            }
        } else if (is_array($data)) {
            if (isset($data['data'])) $data = $data['data'];
            $store['data'] = array();
            foreach ($data as $k=>$i) {
                if (!is_array($i)) {
                    $store['data'][] = array($k, $i);
                } else {
                    if (isset($i['id'])) $id = $i['id'];
                    elseif (isset($i[0])) $id = $i[0];
                    else throw new Vps_Exception("id not found");
                    if (isset($i['value'])) $value = $i['value'];
                    else if (isset($i[1])) $value = $i[1];
                    else throw new Vps_Exception("value not found");
                    $store['data'][] = array($id, $value);
                }
            }
        }
        return $store;
    }
    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == '' || $ret == 'null') $ret = null;
        return $ret;
    }
}
