<?php
/**
 * Für zwei (oder mehr) zusammengeschaltete ComboBoxen (Auswahl in der ersten
 * lädt Daten in der zweiten nach, gespeichert wird nur die zweite wenn nicht anders angegeben)
 *
 * @package Form
 * @deprecated use Kwf_Form_Field_FilterField instead
 **/
class Kwf_Form_Field_ComboBoxFilter extends Kwf_Form_Field_Select
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('comboboxfilter');
        $this->setSave(false);
    }

    //setFilteredCombo(combo)


    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);

        $saveCombo = $this->getFilteredCombo();
        $saveMetaData = $saveCombo->getMetaData($model);

        $filterMetaData = $ret;
        $filterMetaData['xtype'] = 'combobox';

        if (!$saveCombo->getFilterField()) {
            throw new Kwf_Exception("setFilterField(str) must be called for the save-combo-box");
        }

        $data = $saveCombo->getValues();
        if (is_array($data)) {
            $saveMetaData['store']['data'] = array();
            foreach ($data as $k=>$i) {
                $addArray = array();
                foreach ($i as $i2) {
                    $addArray[] = $i2;
                }
                $saveMetaData['store']['data'][] = $addArray;
            }
        }

        $ret['items'] = array(
            $filterMetaData,
            $saveMetaData
        );

        return $ret;
    }
/*
    public function load(Kwf_Model_Row_Interface $row, $postData)
    {
        $ret = parent::load($row, $postData);
        $filteredCombo = $this->getFilteredCombo();
        if ($filteredCombo->getSave() !== false && $row) {
            $ret = array_merge($ret, $filteredCombo->load($row, $postData));
        }
        return $ret;
    }

    public function prepareSave(Kwf_Model_Row_Interface $row, $postData)
    {
        parent::prepareSave($row, $postData);
        $filteredCombo = $this->getFilteredCombo();
        if ($filteredCombo->getSave() !== false) {
            $filteredCombo->prepareSave($row, $postData);
        }
    }
*/
    public function hasChildren()
    {
        return true;
    }

    public function getChildren()
    {
        $ret = parent::getChildren();
        $ret[] = $this->getFilteredCombo();
        return $ret;
    }

    public function processInput($row, $postData)
    {
        $postData = parent::processInput($row, $postData);
        if (!$this->getFilteredCombo()) {
            throw new Kwf_Exception("No filteredCombo set");
        }
        $value = $this->_getValueFromPostData($postData);
        if ($value) {
            $filtered = $this->getFilteredCombo();
            $filtered->setFilterValue($value);
        }
        return $postData;
    }

    public function load($row, $postData = array())
    {
        $ret = parent::load($row, $postData);
        if (!$this->getFilteredCombo()) {
            throw new Kwf_Exception("No filteredCombo set");
        }
        $value = $ret[$this->getFieldName()];
        if ($value) {
            $filtered = $this->getFilteredCombo();
            $filtered->setFilterValue($value);
        }
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $this->setSubmitOnChange(true);
        return parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
    }
}
