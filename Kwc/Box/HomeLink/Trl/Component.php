<?php
class Kwc_Box_HomeLink_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['home'] = $this->getData()->parent->getChildPage(array('home' => true), array());
        return $ret;
    }
}
