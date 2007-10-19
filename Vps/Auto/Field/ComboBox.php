<?php
class Vps_Auto_Field_ComboBox extends Vps_Auto_Field_SimpleAbstract
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

    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        Vps_Auto_Field_Abstract::prepareSave($row, $postData);

        if ($this->getSave() !== false) {
            $fieldName = $this->getFieldName();

            if (isset($postData[$fieldName])) {
                $data = $postData[$fieldName];
                foreach($this->getValidators() as $v) {
                    if (!$v->isValid($data)) {
                        if ($this->getFieldLabel()) $name = $this->getFieldLabel();
                        if ($this->getFieldLabel()) $name = $this->getName();
                        throw new Vps_ClientException($name.": ".implode("<br />\n", $v->getMessages()));
                    }
                }
                if ($data=='') $data = null;
                $this->getData()->save($row, $data);
            }
        }
    }

}
