<?php
class Kwf_Collection_Iterator_FormFieldsFilter extends FilterIterator
{
    public function accept()
    {
        return $this->current() instanceof Kwf_Form_Field_Abstract;
    }
}
