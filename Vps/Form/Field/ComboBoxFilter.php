<?php
/**
 * Für zwei (oder mehr) zusammengeschaltete ComboBoxen (Auswahl in der ersten
 * lädt Daten in der zweiten nach, gespeichert wird nur die zweite wenn nicht anders angegeben)
 **/
class Vps_Form_Field_ComboBoxFilter extends Vps_Form_Field_Select
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('comboboxfilter');
        $this->setSave(false);
    }

    //setFilteredCombo(combo)

    /*
    public function getMetaData()
    {
        $ret = parent::getMetaData();

        $filterMetaData = $this->_filterObject->getMetaData();

        $saveMetaData = $ret;
        $saveMetaData['xtype'] = 'combobox';

        // Breite von Filter übernehmen, wenn gesetzt
        if (!empty($filterMetaData['width'])) {
            $saveMetaData['width'] = $filterMetaData['width'];
        } else if (!empty($saveMetaData['width'])) {
            unset($saveMetaData['width']);
        }

        if (empty($saveMetaData['store']['fields'])) {
            $saveMetaData['store']['fields'] = array('id', 'value', 'filterId');
        }
        if (empty($filterMetaData['store']['fields'])) {
            $filterMetaData['store']['fields'] = array('id', 'value');
        }

        $data = $this->getValues();
        if (is_array($data)) {
            $saveMetaData['store']['data'] = array();
            foreach ($data as $k=>$i) {
                $saveMetaData['store']['data'][] = array($i['id'], $i['value'], $i['filterId']);
            }
        }
        $ret['store'] = $saveMetaData['store'];


        $ret['items'] = array(
            $filterMetaData,
            $saveMetaData
        );

        return $ret;
    }
    */
    public function processInput($row, $postData)
    {
        $postData = parent::processInput($row, $postData);
        if (!$this->getFilteredCombo()) {
            throw new Vps_Exception("No filteredCombo set");
        }
        $value = $this->_getValueFromPostData($postData);
        if ($value) {
            $filtered = $this->getFilteredCombo();
            $filtered->setFilterValue($value);
        }
        return $postData;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix);

        //TODO: Wenn wir mal Ext-Forms im Frontend haben das hier entfernen
        $onclick = "onchange=\"this.form.submit();\"";
        $ret['html'] = str_replace("<select ", "<select $onclick ", $ret['html']);

        $ret['html'] .= '<input type="submit" value="»" />';
        return $ret;
    }
}
