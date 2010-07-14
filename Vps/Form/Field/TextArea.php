<?php
class Vps_Form_Field_TextArea extends Vps_Form_Field_TextField
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('textarea');
        $this->setWidth(100);
        $this->setHeight(60);
    }
    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $name = $this->getFieldName();
        $value = isset($values[$name]) ? $values[$name] : $this->getDefaultValue();
        $ret = Vps_Form_Field_SimpleAbstract::getTemplateVars($values);
        //todo: escapen
        $ret['id'] = str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $ret['html'] = "<textarea id=\"$ret[id]\" name=\"$name$fieldNamePostfix\" ";
        $width = $this->getWidth();
        if (is_numeric($width)) {
            $width .= 'px';
        }
        $ret['html'] .= "style=\"width: $width; height: {$this->getHeight()}px\">";
        $ret['html'] .= $value;
        $ret['html'] .= "</textarea>";
        return $ret;
    }
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Text Area')
        ));
    }
}
