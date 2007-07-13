<?php
class Vpc_Formular_Textbox_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {
            $maxlength = $row->maxlength;
            $width = $row->width;
            $name = $row->name;
        } else {
            
            $width = 50;
            $maxlength = 255;
            $name = 'text';
        }
        
        $return['text'] = '';
        $return['maxlength'] = $maxlength;
        $return['width'] = $width;
        $return['name'] = $name;
        $return['template'] = 'Formular/Textbox.html';
        return $return;
    }
}