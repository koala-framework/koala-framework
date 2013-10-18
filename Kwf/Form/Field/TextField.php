<?php
/**
 * A standard textfield form field
 * @package Form
 */
class Kwf_Form_Field_TextField extends Kwf_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('textfield');
        $this->setInputType('text');
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'emptyText';
        return $ret;
    }

    protected function _addValidators()
    {
        parent::_addValidators();

        // Verwendet bis auf email die Regex von ext/from/VTypes.js
        if ($this->getVtype() === 'email') {
            $this->addValidator(new Kwf_Validate_EmailAddressSimple());
        } else if ($this->getVtype() === 'url') {
            $this->addValidator(new Zend_Validate_Regex('/(((https?)|(ftp)):\/\/([\-\w]+\.)+\w{2,3}(\/[%\-\w]+(\.\w{2,})?)*(([\w\-\.\?\\/+@&#;`~=%!]*)(\.\w{2,})?)*\/?)/i'));
        } else if ($this->getVtype() === 'alpha') {
            $this->addValidator(new Zend_Validate_Regex('/^[a-zA-Z_]+$/'));
        } else if ($this->getVtype() === 'alphanum') {
            $this->addValidator(new Zend_Validate_Regex('/^[a-zA-Z0-9_\-]+$/'));
        } else if ($this->getVtype() === 'num') {
            $this->addValidator(new Zend_Validate_Regex('/^[0-9]+$/'));
        }
        if ($this->getMaxLength()) {
            $this->addValidator(new Kwf_Validate_StringLength(0, $this->getMaxLength()));
        }
    }

    protected function _getOutputValueFromValues($values)
    {
        $name = $this->getFieldName();
        $ret = isset($values[$name]) ? $values[$name] : $this->getDefaultValue();
        return (string)$ret;
    }

    protected function _getInputProperties($values, $fieldNamePostfix, $idPrefix)
    {
        $name = $this->getFieldName();
        $value = $this->_getOutputValueFromValues($values);

        $ret = array();
        $ret['id'] = $idPrefix.str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $cls = $this->getCls();
        if ($this->getClearOnFocus() && $value == $this->getDefaultValue()) {
            $cls .= ' kwfClearOnFocus';
        }
        $style = '';
        if ($this->getWidth()) {
            $style .= "width: ".$this->getWidth()."px; ";
        }
        $ret['type'] = $this->getInputType();
        $ret['name'] = "$name$fieldNamePostfix";
        $value = str_replace(array("\n", "\r"), array(' ', ''), $value);
        $ret['value'] = $value;
        if ($style) $ret['style'] = trim($style);
        if ($cls) $ret['class'] = trim($cls);
        if ($this->getMaxLength()) $ret['maxlength'] = $this->getMaxLength();

        if ($this->getVtype() === 'email') {
            $ret['type'] = 'email';
        } else if ($this->getVtype() === 'url') {
            $ret['type'] = 'url';
        } else if ($this->getVtype() === 'alpha') {
            $ret['pattern'] = '[a-zA-Z_]*';
        } else if ($this->getVtype() === 'alphanum') {
            $ret['pattern'] = '[a-zA-Z0-9_\-]*';
        } else if ($this->getVtype() === 'num') {
            $ret['pattern'] = '[0-9]*';
        }
        if ($this->getAutoComplete() === false) {
            $ret['autoComplete'] = 'off';
        }
        if ($this->getEmptyText()) {
            $ret['placeholder'] = $this->getEmptyText();
        }
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        $prop = $this->_getInputProperties($values, $fieldNamePostfix, $idPrefix);
        $ret['id'] = $prop['id'];
        $ret['html'] = "<input";
        foreach ($prop as $k=>$i) {
            $ret['html'] .= ' '.htmlspecialchars($k).'="'.htmlspecialchars($i).'"';
        }
        $ret['html'] .= " />";
        return $ret;
    }

    /**
     * Set the validator used for this textfield. Validation will be done
     * Server Side and for AutoForms in JavaScript
     *
     * Additional Zend Validators can be added by addValidator
     *
     * Possible values are:
     * - email
     * - url
     * - alpha
     * - alphanum
     * - num
     *
     * @see addValidator
     * @param string
     * @return $this
     */
    public function setVtype($value)
    {
        return $this->setProperty('vtype', $value);
    }

    /**
     * Set the maximum input field length allowed
     *
     * @param bool
     * @return $this
     */
    public function setMaxLength($value)
    {
        return $this->setProperty('maxLength', $value);
    }

    /**
     * Disables browser autocompletion for this field.
     *
     * Used in Frontend Forms only, in backend it's disabled by Ext always
     *
     * @param bool
     * @return $this
     */
    public function setAutoComplete($value)
    {
        return $this->setProperty('autoComplete', $value);
    }
}
