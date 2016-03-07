<?php
//requires jquery.socialshareprivacy bower package
class Kwc_SocialMedia_2ClickButtons_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('2 click social media buttons');
        $ret['componentCategory'] = 'callToAction';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['cssClass'] = 'webStandard webListNone';
        $ret['assetsDefer']['files'][] = 'jquery.socialshareprivacy/jquery.socialshareprivacy.min.js';
        $ret['assetsDefer']['files'][] = 'jquery.socialshareprivacy/socialshareprivacy/socialshareprivacy.css';
        $ret['assetsDefer']['dep'][] = 'jQuery';
        return $ret;
    }

    protected function _getSocialNetworks()
    {
        return (object)array(
            'facebook' => $this->_getRow()->facebook,
            'twitter' => $this->_getRow()->twitter,
            'google' => $this->_getRow()->google
        );
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $socialNetworks = $this->_getSocialNetworks();
        $txtInfo = $this->getData()->trlKwf('2 clicks for more privacy: When you click here the button will be activated and you can send your recommendation. As soon as the button is activated data will be sent to third parties.');
        $ret['config'] = array(
            'showFacebook' => ($socialNetworks->facebook) ? 1 : 0,
            'showTwitter' => ($socialNetworks->twitter) ? 1 : 0,
            'showGoogle' => ($socialNetworks->google) ? 1 : 0,
            'services' => array(
                'facebook' => array(
                    'txtInfo' => $txtInfo,
                    'txtFbOff' => $this->getData()->trlKwf('not connected to Facebook'),
                    'txtFbOn' => $this->getData()->trlKwf('connected to Facebook'),
                    'dummyCaption' => $this->getData()->trlKwf('Recommend'),
                    'language' => $this->getData()->trlKwf('en_US')
                ),
                'twitter' => array(
                    'txtInfo' => $txtInfo,
                    'txtTwitterOff' => $this->getData()->trlKwf('not connected to Twitter'),
                    'txtTwitterOn' => $this->getData()->trlKwf('connected to Twitter'),
                    'dummyCaption' => $this->getData()->trlKwf('Tweet'),
                    'language' => $this->getData()->getLanguage()
                ),
                'gplus' => array(
                    'txtInfo' => $txtInfo,
                    'txtGPlusOff' => $this->getData()->trlKwf('not connected to Google+'),
                    'txtGPlusOn' => $this->getData()->trlKwf('connected to Google+'),
                    'language' => $this->getData()->getLanguage()
                )
            ),
            'txtHelp' => $this->getData()->trlKwf('If you activate these buttons with a click informations will be sent to Facebook, Twitter or Google in the USA and may be stored there.'),
            'settingsPerma' => $this->getData()->trlKwf('Agree permanent activation and data transfer:'),
            'settings' => $this->getData()->trlKwf('settings')
        );
        return $ret;
    }

    public function hasContent()
    {
        $socialNetworks = $this->_getSocialNetworks();
        if ($socialNetworks->facebook || $socialNetworks->twitter || $socialNetworks->google) return true;
        return false;
    }


}

