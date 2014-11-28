<?php
class Kwf_Filter_Row_InsertCurrentDateTime extends Kwf_Filter_Row_CurrentDateTime
{
    public function filter($row)
    {
        if ($row->{$this->_field}) {
            //if value already exists don't change
            return $row->{$this->_field};
        }

        return parent::filter($row);
    }
}
