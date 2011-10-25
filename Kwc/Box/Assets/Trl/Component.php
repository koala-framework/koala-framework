<?php
class Kwc_Box_Assets_Trl_Component extends Kwc_Box_Assets_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['language'] = $this->getData()->getLanguage();
        return $ret;
    }
}
