<?php
/**
 * @package Form
 */
class Kwf_Form_Field_ComboBox extends Kwf_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('combobox');
        $this->setEmptyText(trlKwfStatic('no selection'));
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        $store = $this->_getStoreData();
        if (isset($store['data']) && $store['data']) {
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
        //'<tpl for="."><div class="x2-combo-list-item">{' + this.displayField + '}</div></tpl>';

    //setShowNoSelection
        //keine auswahl anbieten

    //setEmptyText
        //text fuer keine auswahl

    //setFilterValue
        //wird von ComboBoxFilter aufgerufen

    //setDisplayField (name)
    //setListWidth


    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);


        $ret['store'] = $this->_getStoreData();
        if (isset($ret['fields'])) unset($ret['fields']);
        if (isset($ret['values'])) unset($ret['values']);
        if (isset($ret['select'])) unset($ret['select']);

        return $ret;
    }

    public function trlStaticExecute($language = null)
    {
        parent::trlStaticExecute($language);
        $trl = Kwf_Trl::getInstance();

        $values = $this->getProperty('values');
        if (is_array($values)) {
            foreach ($values as $k => $v) {
                $newKey = $k;
                $newValue = $v;
                if (is_string($k)) $newKey = $trl->trlStaticExecute($k, $language); //TODO key nicht (immer) übersetzen
                if (is_string($v)) $newValue = $trl->trlStaticExecute($v, $language);
                else if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if (is_string($v2)) {
                            $newValue[$k2] = $trl->trlStaticExecute($v2, $language);
                        }
                    }
                }

                unset($values[$k]);
                $values[$newKey] = $newValue;
            }
            $this->setProperty('values', $values);
        }
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'emptyText';
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
        } else if ($this->getFilterField() && !$this->getFilterValue()) {
            $store['data'] = array();
        } else if ($data instanceof Zend_Db_Table_Abstract
            || $data instanceof Kwf_Model_Interface
            || $data instanceof Kwf_Db_Table_Rowset_Abstract
            || $data instanceof Kwf_Model_Rowset_Interface
        ) {
            if ($data instanceof Zend_Db_Table_Abstract) {
                $select = $this->getSelect();
                if (!$select) {
                    $select = $data->select();
                }
                if ($this->getFilterField() && $this->getFilterValue()) {
                    $select->where($this->getFilterField().' = ?', $this->getFilterValue());
                }
                $data = $data->fetchAll($select);
            } else if ($data instanceof Kwf_Model_Interface) {
                $select = $this->getSelect();
                if (!$select) {
                    $select = $data->select();
                }
                if ($this->getFilterField() && $this->getFilterValue()) {
                    $select->whereEquals($this->getFilterField(), $this->getFilterValue());
                }
                $data = $data->getRows($select);
            }
            $store['data'] = array();
            foreach ($data as $row) {
                if ($this->getFilterField() && !isset($select)) {
                    if (!$this->getFilterValue()) continue;
                    if ($row->{$this->getFilterField()} != $this->getFilterValue()) {
                        continue;
                    }
                }
                $d = array();
                if ($fields) {
                    foreach ($fields as $f) {
                        $d[] = $row->$f;
                    }
                } else {
                    $d[] = $row->id;
                    $d[] = $row->__toString();
                }
                $store['data'][] = $d;
            }
        } else if (is_array($data)) {
            if (isset($data['data'])) $data = $data['data'];
            $store['data'] = array();
            foreach ($data as $k=>$i) {
                if (!is_array($i)) {
                    $store['data'][] = array($k, $i);
                } else {
                    if ($fields) {
                        $d = array();
                        foreach ($fields as $f) {
                            $d[] = $i[$f];
                        }
                    } else {
                        if (array_key_exists('id', $i)) $id = $i['id'];
                        elseif (array_key_exists(0, $i)) $id = $i[0];
                        else throw new Kwf_Exception("array key 'id' not found");//easier to understand
                        if (array_key_exists('value', $i)) $value = $i['value'];
                        else if (array_key_exists(1, $i)) $value = $i[1];
                        else throw new Kwf_Exception("array key 'value' not found");//easier to understand
                        $d = array($id, $value);
                    }

                    $store['data'][] = $d;
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
