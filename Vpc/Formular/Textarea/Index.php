<?php
class Vpc_Formular_Textarea_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {
            
            $text = $row->text;
            $cols = $row->cols;
            $rows = $row->rows;
            $name = $row->name;
        } else {
            
            $text = "";
            $cols = 20;
            $rows = 20;
            $name = "textArea";
        }
        
      
        $return['text'] = $text;
        $return['cols'] = $rows;
        $return['rows'] = $cols;
        $return['name'] = $name;
        $return['template'] = 'Textarea.html';
        return $return;
    }
}