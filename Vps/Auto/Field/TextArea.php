<?php
class Vps_Auto_Field_TextArea extends Vps_Auto_Field_TextField
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('textarea');
    }
    public function getTemplateVars()
    {
        $name = $this->getFieldName();
        $ret = parent::getTemplateVars();
        //todo: escapen
        $ret['html'] = "<textarea id=\"$name\" name=\"$name\"></textarea>";
        return $ret;
    }
}
