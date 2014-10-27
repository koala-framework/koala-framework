<?php
class Kwc_Advanced_YoutubeWithImage_Youtube_Component extends Kwc_Advanced_Youtube_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        $ret['playerVars']['showinfo'] = 0;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['image'] = $this->getData()->parent->getChildComponent('-image');
        return $ret;
    }
}
