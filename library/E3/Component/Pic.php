<?php
class E3_Component_Pic extends E3_Component_Abstract
{
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        $ret['pic'] = 'files/pics/'.$this->getId().'.jpg';
       	$ret['template'] = 'Pic.html';

        return $ret;
    }
}
