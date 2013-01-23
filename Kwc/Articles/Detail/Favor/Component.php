<?php
class Kwc_Articles_Detail_Favor_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['assets']['files'][] = 'kwf/Kwc/Articles/Detail/Favor/Component.js';
        $ret['assets']['dep'][] = 'KwfSwitchHoverFade';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['favorText'] = trlKwf('save as favorite');
        if ($this->getData()->parent->row->autheduser_is_favourite) {
            $ret['favText'] = trlKwf('delete favorite');
            $ret['cssClass'] .= ' isFavourite';
        }
        $ret['config'] = array(
            'componentId' => $this->getData()->dbId,
            'controllerUrl' => Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl()
        );
        return $ret;
    }
}
