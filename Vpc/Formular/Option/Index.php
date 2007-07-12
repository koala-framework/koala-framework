<?php
class Vpc_Formular_Option_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {
            $text = $row->text;
            $value = $row->value;
            $name = $row->name;
        } else {
            
            $text = "test";
            $value = "test";
            $name = "test";
        }
        
        $return['value'] = $value;
        $return['text'] = $text;
   
        $return['name'] = $name;
        $return['template'] = 'Option.html';
        return $return;
    }
}