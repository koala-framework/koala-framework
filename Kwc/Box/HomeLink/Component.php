<?php
class Kwc_Box_HomeLink_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'kwfup-webStandard';
        $ret['placeholder']['linkText'] = trlKwfStatic('Home');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['home'] = $this->getData()->getSubroot()->getChildPage(array('home' => true), array());
        return $ret;
    }
}
