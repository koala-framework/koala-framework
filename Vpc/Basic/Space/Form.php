<?php
class Vpc_Basic_Space_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);
        $this->fields->add(new Vps_Auto_Field_NumberField('height'))
            ->setFieldLabel('Height')
            ->setWidth(80)
            ->setAllowNegative(false)
            ->setAllowDecimals(false);
    }
}
