<?php
class Vpc_Composite_TextImageLink_Trl_Component extends Vpc_Abstract_Composite_Trl_Component
{
    public static function getSettings($mastetComponentClass)
    {
        $ret = parent::getSettings($mastetComponentClass);
        $ret['ownModel'] = 'Vpc_Composite_TextImageLink_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $this->_getRow();
        $ret['title'] = $row->title;
        $ret['teaser'] = $row->teaser;
        return $ret;
    }
}
