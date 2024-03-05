<?php
/**
 * @package Form
 */
class Kwf_Form_Container_FieldSet extends Kwf_Form_Container_Abstract
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
        $ret = parent::_getTrlProperties();
        $ret[] = 'title';
        return $ret;
    }

    /**
     * Zusammen mit setCheckboxToggle(true)
     */
    public function setCheckboxName($name)
    {
        $this->_checkboxHiddenField = new Kwf_Form_Container_FieldSet_Hidden($name);
        $this->fields->add($this->_checkboxHiddenField);
        return $this;
    }

    /**
     * Set align attribute for legend
     *
     * Used in Frontend Forms only
     *
     * @param string (left|center|right)
     * @return $this
     */
    public function setLegendAlign($value)
    {
        return $this->setProperty('legendAlign', $value);
    }

    //verhindert aufrufen von validate/prepareSave/save etc fuer kinder wenn checkbox nicht gesetzt
    protected function _processChildren($method, $childField, $row, $postData)
    {
        if ($method == 'load') return true;
        if ($this->_checkboxHiddenField && $childField !== $this->_checkboxHiddenField) {
            $n = $this->_checkboxHiddenField->getFieldName();
            if (isset($postData[$n]) && $postData[$n]) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        if (!isset($ret['items'])) {
            throw new Kwf_Exception("Fieldset must contain at least one field.");
        }
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

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        if ($this->getCheckboxToggle() && $this->_checkboxHiddenField) {
            $name = $this->_checkboxHiddenField->getFieldName();
            if (isset($values[$name])) {
                $value = $values[$name];
            } else {
                $value = $this->getDefaultValue();
            }
        }
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        foreach ($ret['items'] as $k=>$i) {
            if (isset($i['item']) && $i['item'] === $this->_checkboxHiddenField) {
                unset($ret['items'][$k]);
            }
        }
        $ret['preHtml'] = '<fieldset';
        $cssClass = $this->getCls();
        if ($this->getCheckboxToggle() && $this->_checkboxHiddenField && !$value) {
            $cssClass .= ' kwfFormContainerFieldSetCollapsed';
        }
        $ret['preHtml'] .= " class=\"{$cssClass}\"";
        $ret['preHtml'] .= '>';
        if ($this->getTitle()) {
            $ret['preHtml'] .= "<legend";
            if ($this->getLegendAlign()) $ret['preHtml'] .= " align=\"{$this->getLegendAlign()}\"";
            $ret['preHtml'] .= '>';
            if ($this->getCheckboxToggle() && $this->_checkboxHiddenField) {
                $n = $this->_checkboxHiddenField->getFieldName();
                $ret['preHtml'] .= "<input type=\"checkbox\" name=\"$n\" ";
                if ($value) $ret['preHtml'] .= 'checked="checked" ';
                $ret['preHtml'] .= "/>";
                $ret['preHtml'] .= "<input type=\"hidden\" name=\"$n-post\" value=\"1\" />";
            }
            $ret['preHtml'] .= " {$this->getTitle()}</legend>";
        }
        $ret['postHtml'] = '</fieldset>';
        return $ret;
    }

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['textfield'] = 'Kwf_Form_Field_TextField';
        $ret['childComponentClasses']['checkbox'] = 'Kwf_Form_Field_Checkbox';
        $ret['childComponentClasses']['select'] = 'Kwf_Form_Field_Select';
        $ret['childComponentClasses']['numberfield'] = 'Kwf_Form_Field_NumberField';
        $ret['childComponentClasses']['textarea'] = 'Kwf_Form_Field_TextArea';
        $ret['childComponentClasses']['fieldset'] = 'Kwf_Form_Container_FieldSet';
        $ret['childComponentClasses']['text'] = 'Kwc_Basic_Text_Component';
        $ret['tablename'] = 'Kwc_Formular_Dynamic_Model';
        $ret['decorator'] = 'Kwc_Formular_Decorator_Label';
        $ret['componentName'] = trlKwf('Fieldset');
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Paragraphs/Panel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwf/Form/Container/FieldSet/Panel.js';
        return $ret;
    }
}
