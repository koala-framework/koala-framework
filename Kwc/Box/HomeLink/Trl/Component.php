<?php
class Kwc_Box_HomeLink_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['home'] = $this->getData()->getSubroot()->getChildPage(array('home' => true), array());
        return $ret;
    }
}
