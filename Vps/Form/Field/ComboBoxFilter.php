<?php
class Vps_Form_Field_ComboBoxFilter extends Vps_Form_Field_ComboBox
{
    private $_filterObject;

    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);

        $this->setEditable(false);
        $this->setTriggerAction('all');
        $this->setXtype('vps.comboboxFilter');
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
            $saveMetaData['store']['fields'] = array('id', 'name', 'filterId');
        }
        if (empty($filterMetaData['store']['fields'])) {
            $filterMetaData['store']['fields'] = array('id', 'name');
        }

        $ret['items'] = array(
            $filterMetaData,
            $saveMetaData
        );
        return $ret;
    }

    public function setFields(array $fields)
    {
        if (!in_array('filterId', $fields)) {
            throw new Vps_Exception('fields \'id\', \'name\' and \'filterId\' must be set when using setFields method');
        }

        return parent::setFields($fields);
    }

    public function setFilterComboBox($filterObject)
    {
        if (! $filterObject instanceof Vps_Form_Field_ComboBox) {
            throw new Vps_Exception('Methode setFilterComboBox der Klasse '
                .'Vps_Form_Field_ComboBoxFilter akzeptiert nur Objekte die eine '
                .'Instanz von Vps_Form_Field_ComboBox sind.');
        }

        $this->_filterObject = $filterObject;
        $this->_filterObject->setSave(false);
        return $this;
    }

}
