<?php
class Vpc_Advanced_CommunityVideo_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['flash']['data'] = array_merge($ret['flash']['data'], $this->_getFlashData());
        return $ret;
    }

    protected function _getFlashData()
    {
        $ret = array();
        $ret['url'] = Vpc_Advanced_CommunityVideo_Component::getFlashUrl($this->getRow());
        $ret['width'] = $this->getRow()->width;
        $ret['height'] = $this->getRow()->height;
        return $ret;
    }

    public function hasContent()
    {
        if (Vpc_Advanced_CommunityVideo_Component::getFlashUrl($this->getRow())) {
            return true;
        }
        return false;
    }
}
