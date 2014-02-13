<?php
abstract class Kwc_User_Detail_Abstract_Component extends Kwc_Abstract_Composite_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->getData()->parent->row;
        return $ret;
    }
}
