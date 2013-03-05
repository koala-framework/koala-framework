<?php
class Kwc_Box_HomeLink_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        $ret['placeholder']['linkText'] = trlKwfStatic('Home');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['home'] = $this->getData()->parent->getChildPage(array('home' => true), array());
        return $ret;
    }
}
