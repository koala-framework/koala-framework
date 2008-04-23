<?php
class Vps_Form_Field_TextField extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('textfield');
    }

    protected function _addValidators()
    {
        parent::_addValidators();

        if ($this->getVType() === 'email') {
            $this->addValidator(new Zend_Validate_EmailAddress());
        } else if ($this->getVType() === 'url') {
            //todo, reuse Zend_Uri::check
        } else if ($this->getVType() === 'alpha') {
            $this->addValidator(new Zend_Validate_Alpha());
        } else if ($this->getVType() === 'alphanum') {
            $this->addValidator(new Zend_Validate_Alnum());
        }
    }

    public function getTemplateVars($values)
    {
        $name = $this->getFieldName();
        if (isset($values[$name])) {
            $value = $values[$name];
        } else {
            $value = '';
        }
        $ret = parent::getTemplateVars($values);
        //todo: escapen
        $ret['html'] = "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" />";
        return $ret;
    }

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'name' => trlVps('Text Field')
        ));
    }
}
