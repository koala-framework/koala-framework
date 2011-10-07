<?php
class Kwc_Basic_Space_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Kwf_Form_Field_NumberField('height'))
            ->setFieldLabel(trlKwf('Height'))
            ->setWidth(80)
            ->setAllowNegative(false)
            ->setAllowDecimals(false);
    }
}
