<?php
class Vps_Auto_Field_Panel extends Vps_Auto_Container_Abstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('panel');
        $this->setBaseCls('x-plain');
    }
}
