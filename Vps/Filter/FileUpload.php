<?php
class Vps_Filter_FileUpload implements Zend_Filter_Interface
{
    public function filter($value)
    {
        $value = str_replace('#', '-', $value);
        return $value;
    }
}
