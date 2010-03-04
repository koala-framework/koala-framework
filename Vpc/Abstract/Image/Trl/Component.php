<?php
class Vpc_Abstract_Image_Trl_Component extends Vpc_Abstract_Composite_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['data'] = $ret['chained'];
        return $ret;
    }
}
