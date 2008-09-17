<?php
class Vpc_Basic_Space_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Vps_Form_Field_NumberField('height'))
            ->setFieldLabel(trlVps('Height'))
            ->setWidth(80)
            ->setAllowNegative(false)
            ->setAllowDecimals(false);
    }
}
