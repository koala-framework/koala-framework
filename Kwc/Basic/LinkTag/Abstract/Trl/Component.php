<?php
class Kwc_Basic_LinkTag_Abstract_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['linkTitle'] = $ret['data']->getLinkTitle();
        return $ret;
    }
    public function hasContent()
    {
        if ($this->getData()->url) {
            return true;
        } else {
            return false;
        }
    }
}
