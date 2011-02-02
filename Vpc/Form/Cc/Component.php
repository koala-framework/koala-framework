<?php
class Vpc_Form_Cc_Component extends Vpc_Chained_Cc_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['action'] = $this->getData()->url;
        return $ret;
    }
}
