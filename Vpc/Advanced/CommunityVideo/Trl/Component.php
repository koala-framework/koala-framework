<?php
class Vpc_Advanced_CommunityVideo_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $url = $this->getRow()->url;
        if ($url) {
            $ret['flash']['data']['url'] = Vpc_Advanced_CommunityVideo_Component::getFlashUrl($this->getRow());
            if ($this->getRow()->width) $ret['flash']['data']['width'] = $this->getRow()->width;
            if ($this->getRow()->height) $ret['flash']['data']['height'] = $this->getRow()->height;
        }

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
