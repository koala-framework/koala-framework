<?php
/**
 * @package Form
 */
class Kwf_Form_Field_Password extends Kwf_Form_Field_TextField
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setInputType('password');
        $this->setAllowTags(true);
    }
}
