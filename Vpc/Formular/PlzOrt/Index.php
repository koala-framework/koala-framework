<?php
class Vpc_Formular_PlzOrt_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {
            $c1 = $this->createComponent('Vpc_Formular_Textbox_Index', 0, $row->plz_id);
            $c2 = $this->createComponent('Vpc_Formular_Textbox_Index', 0, $row->ort_id);
        } else {
            
            $text = "";
            $size = 50;
            $length = 255;
            $name = "textBox";
        }
        
        $return['c1'] = $c1;
        $return['c2'] = $c2;
        $return['template'] = 'PlzOrt.html';
        return $return;
    }
}