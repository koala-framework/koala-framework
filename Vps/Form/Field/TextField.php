<?php
class Vps_Form_Field_TextField extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('textfield');
        $this->setInputType('text');
    }

    public function __call($n, $v)
    {
        if ($n == 'setVType') {
            $e = new Vps_Exception('use setVtype instead of setVType');
            $e->logOrThrow();
            $n = 'setVtype';
        }
        return parent::__call($n, $v);
    }

    protected function _addValidators()
    {
        parent::_addValidators();

        // Verwendet bis auf email die Regex von ext/from/VTypes.js
        if ($this->getVtype() === 'email') {
            $this->addValidator(new Vps_Validate_EmailAddressSimple());
        } else if ($this->getVtype() === 'url') {
            $this->addValidator(new Zend_Validate_Regex('/(((https?)|(ftp)):\/\/([\-\w]+\.)+\w{2,3}(\/[%\-\w]+(\.\w{2,})?)*(([\w\-\.\?\\/+@&#;`~=%!]*)(\.\w{2,})?)*\/?)/i'));
        } else if ($this->getVtype() === 'alpha') {
            $this->addValidator(new Zend_Validate_Regex('/^[a-zA-Z_]+$/'));
        } else if ($this->getVtype() === 'alphanum') {
            $this->addValidator(new Zend_Validate_Regex('/^[a-zA-Z0-9_]+$/'));
        }
        if ($this->getMaxLength()) {
            $this->addValidator(new Zend_Validate_StringLength(0, $this->getMaxLength()+1));
        }
    }

    protected function _getOutputValueFromValues($values)
    {
        $name = $this->getFieldName();
        return isset($values[$name]) ? $values[$name] : $this->getDefaultValue();
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $name = $this->getFieldName();
        $value = $this->_getOutputValueFromValues($values);
        $ret = parent::getTemplateVars($values);

        $value = htmlspecialchars($value);
        $name = htmlspecialchars($name);
        $ret['id'] = str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $cls = $this->getCls();
        if ($this->getClearOnFocus() && $value == $this->getDefaultValue()) {
            $cls = trim($cls.' vpsClearOnFocus');
        }
        $style = '';
        if ($this->getWidth()) {
            $style = "style=\"width: ".$this->getWidth()."px\" ";
        }
        $ret['html'] = "<input type=\"".$this->getInputType()."\" id=\"$ret[id]\" ".
                        "name=\"$name$fieldNamePostfix\" value=\"$value\" ".
                        $style.
                        ($cls ? "class=\"$cls\"" : '').
                        "maxlength=\"{$this->getMaxLength()}\" />";
        return $ret;
    }

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Text Field'),
            'default' => array(
                'width' => 150,
                'max_length' => 100
            )
        ));
    }
}
