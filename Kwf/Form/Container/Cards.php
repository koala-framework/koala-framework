<?php
/**
 * @package Form
 */
class Kwf_Form_Container_Cards extends Kwf_Form_Container_Abstract
{
    private $_combobox;

    public function __construct($name = null, $fieldLabel = null)
    {
        $this->fields = new Kwf_Collection_FormFields(null, 'Kwf_Form_Container_Card');

        $this->_combobox = new Kwf_Form_Field_Select($name);
        $this->_combobox->setWidth(150)
            ->setListWidth(150);

        parent::__construct($name);
        $this->setFieldLabel($fieldLabel);
        $this->setBaseCls('x2-plain');
        $this->setXtype('kwf.cards');
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

    public function setName($v)
    {
        parent::setName($v.'_cards');
        $this->_combobox->setName($v);
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

        if (!$this->_combobox->getValues()) {
            $comboboxData = array();
            foreach ($this->fields as $card) {
                if ($card instanceof Kwf_Form_Container_Card) {
                    $comboboxData[$card->getName()] = $card->getTitle();
                }
            }
            $this->_combobox->setValues($comboboxData);
        }

        $cardItems = $this->fields->getMetaData($model);
        $cardItems = array_values($cardItems);
        foreach ($cardItems as $k => $v) {
            unset($cardItems[$k]['title']);
        }

        $ret['items'] = array(
            $this->_combobox->getMetaData($model),
            array(
                'layout' => 'card',
                'baseCls' => 'x2-plain',
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

    public function getFrontendMetaData()
    {
        $ret = parent::getFrontendMetaData();
        $ret['combobox'] = $this->getCombobox()->getFieldName();
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = array();

        $name = $this->getCombobox()->getFieldName();
        $value = isset($values[$name]) ? $values[$name] : $this->getCombobox()->getDefaultValue();
        
        $comboboxData = array();
        foreach ($this->fields as $card) {
            if ($card instanceof Kwf_Form_Container_Card) {
                $comboboxData[$card->getName()] = $card->getTitle();
            }
        }
        $this->getCombobox()->setValues($comboboxData);
        $this->getCombobox()->setSubmitOnChange(true);
        $r = $this->getCombobox()->getTemplateVars($values, $fieldNamePostfix, $idPrefix);

        $ret['items'] = array(
            $r
        );
        foreach ($this->fields as $card) {
            $r = $card->getTemplateVars($values, $fieldNamePostfix, $idPrefix);
            $inactive = '';
            if ($card->getName() != $value) {
                $inactive = ' inactive';
            }
            if (!isset($r['preHtml'])) $r['preHtml'] = '';
            if (!isset($r['postHtml'])) $r['postHtml'] = '';
            $r['preHtml'] = '<div class="kwfFormCard'.$inactive.'">'.$r['preHtml'];
            $r['postHtml'] = $r['postHtml'].'</div>';
            $ret['items'][] = $r;
        }

        $ret['preHtml'] = '';
        $ret['item'] = $this;

        return $ret;
    }
}
