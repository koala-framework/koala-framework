<?php
class Vps_Form_Field_Hidden extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null)
    {
        parent::__construct($field_name);
        $this->setXtype('hidden');
    }
    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $name = $this->getFieldName();
        $value = $values[$name];

        $ret = parent::getTemplateVars($values);

        $value = htmlspecialchars($value);
        $name = htmlspecialchars($name);
        $ret['html'] = "<input type=\"hidden\" ".
                        "name=\"$name$fieldNamePostfix\" value=\"$value\" />";
        return $ret;
    }
}
