<?php
class Vpc_Formular_Index extends Vpc_Paragraphs_Abstract
{
    public function getTemplateVars($mode)
    {
        $vars = parent::getTemplateVars($mode);
        $vars['template'] = 'Formular.html';
        return $vars;
    }
    
}