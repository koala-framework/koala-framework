<?php
class Vps_Form_Container_Cards extends Vps_Form_Container_Abstract
{
    private $_combobox;

    public function __construct($name = null, $fieldLabel = null)
    {
        $this->fields = new Vps_Collection_FormFields(null, 'Vps_Form_Container_Card');
        parent::__construct();
        $this->setBaseCls('x-plain');
        $this->setXtype('vps.cards');
        $this->setLayout('form');

        $this->_combobox = $this->fields->add(new Vps_Form_Field_Select($name, $fieldLabel))
            ->setWidth(150)
            ->setListWidth(150);
    }

    public function getCombobox()
    {
        return $this->_combobox;
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

    public function validate($row, $postData)
    {
        foreach ($this->fields as $card) {
            if ($card != $this->_combobox
                && $card->getName() != $postData[$this->_combobox->getFieldName()]) {
                $card->setInternalSave(false);
            } else {
                $card->setInternalSave(true);
            }
        }
        return parent::validate($row, $postData);
    }

    public function load($parentRow, $postData = array())
    {
        $row = (object)$this->_getRowByParentRow($parentRow);
        $ret = array();
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                if ($field instanceof Vps_Form_Container_Card) {
                    if ($field->getName() == $row->{$this->_combobox->getName()}) {
                        $ret = array_merge($ret, $field->load($row, $postData));
                    }
                } else {
                    $ret = array_merge($ret, $field->load($row, $postData));
                }
            }
        }
        return $ret;
    }
}
