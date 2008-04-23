<?php
class Vps_Collection_Iterator_RecursiveFormFields extends Vps_Collection_Iterator_Recursive
{
    public function hasChildren()
    {
        if ($this->current() instanceof Vps_Form_Field_Abstract) {
            return $this->current()->hasChildren();
        } else {
            return false;
        }
    }

    public function getChildren()
    {
        return new Vps_Collection_Iterator_Recursive($this->current()->getChildren());
    }
}
