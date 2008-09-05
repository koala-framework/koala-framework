<?php
class Vps_Form_Field_Hidden extends Vps_Form_Field_SimpleAbstract
{
    public function getMetaData()
    {
        return null;
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
        $ret['html'] = "<input type=\"hidden\" ".
                        "name=\"$name$fieldNamePostfix\" value=\"$value\" />";
        return $ret;
    }
}
