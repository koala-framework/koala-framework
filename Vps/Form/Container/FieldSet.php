<?php
class Vps_Form_Container_FieldSet extends Vps_Form_Container_Abstract
{
    private $_checkboxHiddenField = null;
    public function __construct($title = null)
    {
        parent::__construct();
        $this->setTitle($title);
        $this->setAutoHeight(true);
        $this->setBorder(true);
        $this->setXtype('fieldset');
        $this->setBaseCls(null);
    }

    protected function _getTrlProperties()
    {
        return array('title');
    }

    /**
     * Zusammen mit setCheckboxToggle(true)
     */
    public function setCheckboxName($name)
    {
        $this->_checkboxHiddenField = new Vps_Form_Container_FieldSet_Hidden($name);
        $this->fields->add($this->_checkboxHiddenField);
        return $this;
    }

    //ob checkbox nicht gesetzt ist und dadurch kind-felder nicht gespeichert/validiert etc werden dürfen
    private function _isHidden($postData)
    {
        if ($this->_checkboxHiddenField) {
            $n = $this->_checkboxHiddenField->getFieldName();
            if (isset($postData[$n]) && $postData[$n]) {
                return true;
            }
        }
        return false;
    }

    //verhindert aufrufen von validate/prepareSave/save etc fuer kinder wenn checkbox nicht gesetzt
    protected function _processChildren($method, $childField, $row, $postData)
    {
        if ($childField === $this->_checkboxHiddenField) return true;
        return (bool)$this->_isHidden($postData);
    }

    public function load($row, $postData = array())
    {
        //komplett überschrieben damit wir die row bei deaktivieren feldern nicht uebergeben

        $ret = array();
        if ($this->_checkboxHiddenField) {
            //_checkboxHiddenField immer row übergeben
            $ret = array_merge($ret, $this->_checkboxHiddenField->load($row, $postData));
        }

        if ($this->_isHidden($postData)) {
            //wenn checkbox nicht gesetzt, keine row übergeben
            $row = null;
        }

        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                if ($field !== $this->_checkboxHiddenField) { //_checkboxHiddenField wurde bereits oben aufgerufen
                    $ret = array_merge($ret, $field->load($row, $postData));
                }
            }
        }
        return $ret;
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        foreach ($ret['items'] as $k=>$i) {
            if ($i == 'hidden') {
                unset($ret['items'][$k]);
            }
        }
        $ret['items'] = array_values($ret['items']);
        if ($this->_checkboxHiddenField) {
            $ret['checkboxName'] = $this->_checkboxHiddenField->getFieldName();
        }
        return $ret;
    }

    public function getTemplateVars($values)
    {
        if ($this->getCheckboxToggle() && $this->_checkboxHiddenField) {
            $name = $this->_checkboxHiddenField->getFieldName();
            if (isset($values[$name])) {
                $value = $values[$name];
            } else {
                $value = $this->getDefaultValue();
            }
        }
        $ret = parent::getTemplateVars($values);
        foreach ($ret['items'] as $k=>$i) {
            if ($i['item'] === $this->_checkboxHiddenField) {
                unset($ret['items'][$k]);
            }
        }
        $ret['preHtml'] = '<fieldset';
        if ($this->getCheckboxToggle() && $this->_checkboxHiddenField && !$value) {
            $ret['preHtml'] .= ' class="vpsFormContainerFieldSetCollapsed"';
        }
        $ret['preHtml'] .= '>';
        if ($this->getTitle()) {
            $ret['preHtml'] .= "<legend>";
            if ($this->getCheckboxToggle() && $this->_checkboxHiddenField) {
                $n = $this->_checkboxHiddenField->getFieldName();
                $ret['preHtml'] .= "<input type=\"checkbox\" name=\"$n\" ";
                if ($value) $ret['preHtml'] .= 'checked="checked" ';
                $ret['preHtml'] .= "/>";
            }
            $ret['preHtml'] .= " {$this->getTitle()}</legend>";
        }
        $ret['postHtml'] = '</fieldset>';
        return $ret;
    }

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['textfield'] = 'Vps_Form_Field_TextField';
        $ret['childComponentClasses']['checkbox'] = 'Vps_Form_Field_Checkbox';
        $ret['childComponentClasses']['select'] = 'Vps_Form_Field_Select';
        $ret['childComponentClasses']['numberfield'] = 'Vps_Form_Field_NumberField';
        $ret['childComponentClasses']['textarea'] = 'Vps_Form_Field_TextArea';
        $ret['childComponentClasses']['fieldset'] = 'Vps_Form_Container_FieldSet';
        $ret['childComponentClasses']['text'] = 'Vpc_Basic_Text_Component';
        $ret['tablename'] = 'Vpc_Formular_Dynamic_Model';
        $ret['decorator'] = 'Vpc_Formular_Decorator_Label';
        $ret['componentName'] = trlVps('Fieldset');
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/Panel.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vps/Form/Container/FieldSet/Panel.js';
        return $ret;
    }
}
