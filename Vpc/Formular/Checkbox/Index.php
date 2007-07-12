<?php
class Vpc_Formular_Checkbox_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {
            
            $value = $row->value;
            $name = $row->name;
            $text = $row->text;
            if($row->checked == 1){
                $checked = "checked";
            }
        } else {
            
            $checked = "";
            $value = "test";
            $name = "test";
            $text = "default";
        }
        
        $return['value'] = $value;
        $return['checked'] = $checked;   
        $return['name'] = $name;
        $return['text'] = $text;
        $return['template'] = 'Checkbox.html';
        return $return;
    }
}