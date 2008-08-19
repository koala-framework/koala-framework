<?php
class Vps_Form_Field_TextField extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('textfield');
        $this->setInputType('text');
    }

    protected function _addValidators()
    {
        parent::_addValidators();

        if ($this->getVType() === 'email') {
            $this->addValidator(new Vps_Validate_EmailAddress());
        } else if ($this->getVType() === 'url') {
            //todo, reuse Zend_Uri::check
        } else if ($this->getVType() === 'alpha') {
            $this->addValidator(new Vps_Validate_Alpha());
        } else if ($this->getVType() === 'alphanum') {
            $this->addValidator(new Vps_Validate_Alnum());
        }
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $name = $this->getFieldName();
        if (isset($values[$name])) {
            $value = $values[$name];
        } else {
            $value = $this->getDefaultValue();
        }
        $ret = parent::getTemplateVars($values);

        $value = htmlspecialchars($value);
        $name = htmlspecialchars($name);
        $ret['id'] = str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $ret['html'] = "<input type=\"".$this->getInputType()."\" id=\"$ret[id]\" ".
                        "name=\"$name$fieldNamePostfix\" value=\"$value\" ".
                        "style=\"width: {$this->getWidth()}px\" ".
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
