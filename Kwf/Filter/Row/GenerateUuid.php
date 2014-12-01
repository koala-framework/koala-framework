<?php
class Kwf_Filter_Row_GenerateUuid extends Kwf_Filter_Row_Abstract
{
    public function filter($row)
    {
        if ($row->{$this->_field}) {
            //if value already exists don't change
            return $row->{$this->_field};
        }
        return Kwf_Filter_GenerateUuid::filter(null);
    }
}
