<?php
/**
 * Für zwei (oder mehr) zusammengeschaltete ComboBoxen (Auswahl in der ersten
 * lädt Daten in der zweiten nach, gespeichert wird nur die zweite wenn nicht anders angegeben)
 **/
class Vps_Form_Field_ComboBoxFilter extends Vps_Form_Field_ComboBox
{
    private $_filterObject;

    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);

        $this->setEditable(false);
        $this->setTriggerAction('all');
        $this->setXtype('comboboxfilter');
    }

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

    public function setFilterComboBox($filterObject, $save = false)
    {
        if (! $filterObject instanceof Vps_Form_Field_ComboBox) {
            throw new Vps_Exception('Methode setFilterComboBox der Klasse '
                .'Vps_Form_Field_ComboBoxFilter akzeptiert nur Objekte die eine '
                .'Instanz von Vps_Form_Field_ComboBox sind.');
        }

        $this->_filterObject = $filterObject;
        if (!$save) $this->_filterObject->setSave(false);
        return $this;
    }

}
