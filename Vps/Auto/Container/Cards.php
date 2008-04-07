<?php
class Vps_Auto_Container_Cards extends Vps_Auto_Container_Abstract
{
    private $_combobox;

    public function __construct($name = null, $fieldLabel = null)
    {
        $this->fields = new Vps_Collection_FormFields(null, 'Vps_Auto_Container_Card');
        parent::__construct();
        $this->setBaseCls('x-plain');
        $this->setXtype('vps.cards');
        $this->setLayout('form');

        $this->_combobox = $this->fields->add(new Vps_Auto_Field_Select($name, $fieldLabel));
    }

    public function setFieldLabel($value)
    {
        $this->_combobox->setFieldLabel($value);
        return $this;
    }

    public function setDefaultValue($value)
    {
        $this->_combobox->setDefaultValue($value);
        return $this;
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();

        $comboboxData = array();
        foreach ($this->fields as $card) {
            if ($card != $this->_combobox) {
                $comboboxData[$card->getName()] = $card->getTitle();
            }
        }
        $this->_combobox->setValues($comboboxData);

        $cardItems = $this->fields->getMetaData();
        unset($cardItems[0]); //die combobox
        $cardItems = array_values($cardItems);
        foreach ($cardItems as $k => $v) {
            unset($cardItems[$k]['title']);
        }

        $ret['items'] = array(
            $this->_combobox->getMetaData(),
            array(
                'layout' => 'card',
                'baseCls' => 'x-plain',
                'border' => false,
                'items' => $cardItems
            )
        );

        return $ret;
    }

    public function prepareSave($row, $postData)
    {
        foreach ($this->fields as $card) {
            if ($card != $this->_combobox
                && $card->getName() != $postData[$this->_combobox->getFieldName()]) {
                $card->setSave(false);
            }
        }
        parent::prepareSave($row, $postData);
    }
}
