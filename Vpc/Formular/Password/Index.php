<?php
class Vpc_Formular_Password_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {
            $length = $row->length;
            $password = $row->password;
            $name = $row->name;
            $size = $row->size;
        } else {
            $name = "password";
            $password = "";
            $size = 50;
            $length = 255;
        }
        
        $return['length'] = $length;
        $return['password'] = $password;
        $return['name'] = $name;
        $return['size'] = $size;
        $return['template'] = 'Password.html';
        return $return;
    }
}