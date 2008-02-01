<?php
class Vpc_Basic_LinkTag_Intern_Field extends Vps_Auto_Field_TextField
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('vpclink');
    }
}
