<?php
class Kwc_Composite_TextImageLink_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($mastetComponentClass)
    {
        $ret = parent::getSettings($mastetComponentClass);
        $ret['ownModel'] = 'Kwc_Composite_TextImageLink_Model';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $row = $this->_getRow();
        $ret['title'] = $row->title;
        $ret['teaser'] = $row->teaser;
        return $ret;
    }
}
