<?php
class Vps_Form_Container_Cards extends Vps_Form_Container_Abstract
{
    private $_combobox;

    public function __construct($name = null, $fieldLabel = null)
    {
        $this->fields = new Vps_Collection_FormFields(null, 'Vps_Form_Container_Card');

        $this->_combobox = new Vps_Form_Field_Select($name, $fieldLabel);
        $this->_combobox->setWidth(150)
            ->setListWidth(150);

        parent::__construct();
        $this->setFieldLabel($fieldLabel);
        $this->setBaseCls('x-plain');
        $this->setXtype('vps.cards');
        $this->setLayout('form');
    }

    public function setNamePrefix($v)
    {
        parent::setNamePrefix($v);
        $this->_combobox->setNamePrefix($this->fields->getFormName());
        return $this;
    }

    public function hasChildren()
    {
        return true;
    }

    public function getChildren()
    {
        $ret = array($this->_combobox);
        $ret = array_merge($ret, parent::getChildren()->getArray());
        return $ret;
    }

    public function getCombobox()
    {
        return $this->_combobox;
    }

    //um zB die combobox durch radios zu ersetzen
    public function setCombobox($box)
    {
        $this->_combobox = $box;
        $this->_combobox->setFormName($this->fields->getFormName());
        return $this;
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

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);

        $comboboxData = array();
        foreach ($this->fields as $card) {
            if ($card instanceof Vps_Form_Container_Card) {
                $comboboxData[$card->getName()] = $card->getTitle();
            }
        }
        $this->_combobox->setValues($comboboxData);

        $cardItems = $this->fields->getMetaData($model);
        $cardItems = array_values($cardItems);
        foreach ($cardItems as $k => $v) {
            unset($cardItems[$k]['title']);
        }

        $ret['items'] = array(
            $this->_combobox->getMetaData($model),
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
