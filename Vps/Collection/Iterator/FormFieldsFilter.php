<?php
class Vps_Collection_Iterator_FormFieldsFilter extends FilterIterator
{
    public function accept()
    {
        return $this->current() instanceof Vps_Form_Field_Abstract;
    }
}
