<?php
class Vpc_Basic_Html_Trl_Component extends Vpc_Abstract_Composite_Trl_Component
{
    public static function getSettings($childComponentClass)
    {
        $ret = parent::getSettings($childComponentClass);
        $ret['ownModel'] = 'Vpc_Basic_Html_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->getRow()->content;
        return $ret;
    }

    public function hasContent()
    {
        return trim($this->getRow()->content) != '';
    }
}
