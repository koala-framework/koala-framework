<?php
/**
 * @package Form
 */
class Kwf_Form_Field_ColorField extends Kwf_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('colorfield');
    }
}
