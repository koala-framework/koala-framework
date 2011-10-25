<?php
class Kwf_Collection_Iterator_RecursiveFormFields extends Kwf_Collection_Iterator_Recursive
{
    public function hasChildren()
    {
        if ($this->current() instanceof Kwf_Form_Field_Abstract) {
            return $this->current()->hasChildren();
        } else {
            return false;
        }
    }

    public function getChildren()
    {
        return new Kwf_Collection_Iterator_RecursiveFormFields($this->current()->getChildren());
    }
}
