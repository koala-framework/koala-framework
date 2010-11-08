<?php
class Vps_Form_Container_Cards extends Vps_Form_Container_Abstract
{
    private $_combobox;

    public function __construct($name = null, $fieldLabel = null)
    {
        $this->fields = new Vps_Collection_FormFields(null, 'Vps_Form_Container_Card');

        $this->_combobox = new Vps_Form_Field_Select($name);
        $this->_combobox->setWidth(150)
            ->setListWidth(150);

        parent::__construct();
        $this->setFieldLabel($fieldLabel);
        $this->setBaseCls('x-plain');
        $this->setXtype('vps.cards');
        $this->setLayout('form');
    }

    public function __clone()
    {
        parent::__clone();
        $this->_combobox = clone $this->_combobox;
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

    public function setAllowBlank($value)
    {
        $this->_combobox->setAllowBlank($value);
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

    //verhindert aufrufen von validate/prepareSave/save etc fuer kinder wenn card nicht ausgewählt
    protected function _processChildren($method, $childField, $row, $postData)
    {
        if ($method == 'load') return true;
        if ($childField === $this->_combobox) return true;

        //wenn card nicht gewählt, nicht aufrufen
        $value = isset($postData[$this->_combobox->getFieldName()]) ? $postData[$this->_combobox->getFieldName()] : $this->_combobox->getDefaultValue();
        return $childField->getName() == $value;
    }

    public function load($row, $postData = array())
    {
        //komplett überschrieben damit wir die row bei deaktivieren cards nicht uebergeben

        $ret = $this->_combobox->load($row, $postData); //combobox immer laden, wert brauchen wir auch für auswahl

        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                if ($field === $this->_combobox) continue; //schon oben aufgerufen
                $r = $row;
                if ($field->getName() != $ret[$this->_combobox->getFieldName()]) {
                    //wenn card nicht gewählt, keine row übergeben
                    $r = null;
                }
                $ret = array_merge($ret, $field->load($r, $postData));
            }
        }
        return $ret;
    }

    public function getTemplateVars($values)
    {
        $ret = array();
        
        $name = $this->getCombobox()->getFieldName();
        $value = isset($values[$name]) ? $values[$name] : $this->getCombobox()->getDefaultValue();
        
        $comboboxData = array();
        foreach ($this->fields as $card) {
            if ($card instanceof Vps_Form_Container_Card) {
                $comboboxData[$card->getName()] = $card->getTitle();
            }
        }
        $this->getCombobox()->setValues($comboboxData);
        $this->getCombobox()->setSubmitOnChange(true);
        $r = $this->getCombobox()->getTemplateVars($values);
        $ret['preHtml'] = $r['html'];
        $ret['item'] = $r['item'];
        
        foreach ($this->fields as $card) {
            if ($card->getName() != $value) continue;
            $ret['items'][] = $card->getTemplateVars($values);
        }
        
        return $ret;
    }
}
