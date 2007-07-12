<?php
class Vpc_Formular_FileUpload_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {
            $length = $row->length;
            $accept = $row->accept;
            $size = $row->size;
            $name = $row->name;
        } else {
            
            $accept = "text/*";
            $size = 50;
            $length = 255;
            $name = "textBox";
        }
        
        $return['length'] = $length;
        $return['accept'] = $accept;
        $return['size'] = $size;
        $return['name'] = $name;
        $return['template'] = 'FileUpload.html';
        return $return;
    }
}