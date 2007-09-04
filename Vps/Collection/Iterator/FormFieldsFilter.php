<?php
class Vps_Collection_Iterator_FormFieldsFilter extends FilterIterator
{
    public function accept()
    {
        p($this->current());
        return $this->current() instanceof Vps_Auto_Field_Abstract;
    }
}
