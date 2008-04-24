<?php
class Vps_Form_Field_TextArea extends Vps_Form_Field_TextField
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('textarea');
    }
    public function getTemplateVars($values)
    {
        $name = $this->getFieldName();
        if (isset($values[$name])) {
            $value = $values[$name];
        } else {
            $value = $this->getDefaultValue();
        }
        $ret = Vps_Form_Field_SimpleAbstract::getTemplateVars($values);
        //todo: escapen
        $ret['html'] = "<textarea id=\"$name\" name=\"$name\" ";
        $ret['html'] .= "style=\"width: {$this->getWidth()}px; height: {$this->getHeight()}px\">";
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
