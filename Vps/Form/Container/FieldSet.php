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

    /**
     * Zusammen mit setCheckboxToggle(true)
     */
    public function setCheckboxName($name)
    {
        $this->_checkboxHiddenField = new Vps_Form_Container_FieldSet_Hidden($name);
        $this->fields->add($this->_checkboxHiddenField);
        return $this;
    }

    public function validate($row, $postData)
    {
        if ($this->_checkboxHiddenField) {
            $n = $this->_checkboxHiddenField->getFieldName();
            if (!isset($postData[$n]) || !$postData[$n]) {
                foreach ($this->fields as $f) {
                    if ($f != $this->_checkboxHiddenField) {
                        $f->setInternalSave(false);
                    }
                }
            }
        }
        return parent::validate($row, $postData);
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        if ($this->_checkboxHiddenField) {
            $ret['checkboxName'] = $this->_checkboxHiddenField->getFieldName();
        }
        return $ret;
    }
    

    public function getTemplateVars($values)
    {
        $name = $this->getFieldName();
        if (isset($values[$name])) {
            $value = $values[$name];
        } else {
            $value = $this->getDefaultValue();
        }
        $ret = parent::getTemplateVars($values);
        $ret['preHtml'] = '<fieldset>';
        if ($this->getTitle()) {
            $ret['preHtml'] .= "<legend>{$this->getTitle()}</legend>";
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
