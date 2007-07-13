<?php
class Vpc_Formular_PlzOrt_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $c1 = $this->createComponent('Vpc_Formular_Textbox_Index', $this->getComponentId(), 1);
        $c2 = $this->createComponent('Vpc_Formular_Textbox_Index', $this->getComponentId(), 2);
        
        $return['c1'] = $c1->getTemplateVars('');
        $return['c2'] = $c2->getTemplateVars('');
        $return['template'] = 'Formular/PlzOrt.html';

        return $return;
    }
}