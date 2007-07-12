<?php
class Vpc_Formular_Email_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {
            $length = $row->length;
            $email = $row->email;
            $size = $row->size;
            $name = $row->name;
        } else {
            
            $name = "name";
            $size = 50;
            $length = 255;
            $email = "email";
        }
        
        $return['length'] = $length;
        $return['email'] = $email;
        $return['size'] = $size;
        $return['name'] = $name;
        $return['template'] = 'Email.html';
        return $return;
    }
}