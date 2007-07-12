<?php
class Vpc_Formular_HiddenField_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {
            $value = $row->value;          
            $name = $row->name;
        } else {
            $value = "";
            $name = "hiddenField";
        }
        
        $return['value'] = $value;
        $return['name'] = $name;
        $return['template'] = 'HiddenField.html';
        return $return;
    }
}