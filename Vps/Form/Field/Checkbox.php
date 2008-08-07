<?php
class Vps_Form_Field_Checkbox extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('checkbox');
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $name = $this->getFieldName();
        if (isset($values[$name])) {
            $value = true;
        } else {
            $value = $this->getDefaultValue();
        }
        $ret = parent::getTemplateVars($values);
        //todo: escapen
        $ret['id'] = $name.$fieldNamePostfix;
        $ret['html'] = "<input type=\"checkbox\" id=\"$ret[id]\" name=\"$name$fieldNamePostfix\" ";
        if ($value) $ret['html'] .= 'checked="checked" ';
        $ret['html'] .= "/>";
        if ($this->getBoxLabel()) {
            $ret['html'] .= ' '.$this->getBoxLabel();
        }
        return $ret;
    }

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Checkbox')
        ));
    }
}
