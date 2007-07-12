<?php
class Vpc_Formular_Textbox_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {
            $length = $row->length;
            $text = $row->text;
            $size = $row->size;
            $name = $row->name;
        } else {
            
            $text = "";
            $size = 50;
            $length = 255;
            $name = "textBox";
        }
        
        $return['length'] = $length;
        $return['text'] = $text;
        $return['size'] = $size;
        $return['name'] = $name;
        $return['template'] = 'Textbox.html';
        return $return;
    }
}