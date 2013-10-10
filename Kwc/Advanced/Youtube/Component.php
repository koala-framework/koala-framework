<?php
class Kwc_Advanced_Youtube_Component extends Kwc_Abstract
{
    const REGEX = '/(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/';
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Youtube');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['assets']['dep'][] = 'KwfYoutubePlayer';
        $ret['assets']['files'][] = 'kwf/Kwc/Advanced/Youtube/Component.js';
        
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';

        $ret['videoWidth'] = 900;
        $ret['playerVars'] = array(
            'rel' => 0,
            'iv_load_policy' => 3,
            'wmode' => 'opaque'
        );
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (!isset($settings['videoWidth'])) {
            throw new Kwf_Exception("videoWidth setting has to be set. Component: '$componentClass'");
        }
        if (!isset($settings['playerVars'])) {
            throw new Kwf_Exception("playerVars setting has to be set. Component: '$componentClass'");
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        preg_match(self::REGEX, $ret['row']->url, $matches);
        $config = array(
            'videoId' => $matches[0],
            'width' => ($ret['row']->videoWidth) ? $ret['row']->videoWidth : $this->_getSetting('videoWidth'),
            'height' => 0
        );
        if ($d = $ret['row']->dimensions) {
            if ($d == '16x9') {
                $config['height'] = ($config['width'] / 16) * 9;
            } else if ($d == '4x3') {
                $config['height'] = ($config['width'] / 4) * 3;
            }
        }
        $ret['config'] = array_merge($config, array('playerVars' => $this->_getSetting('playerVars')));
        $ret['config']['playerVars']['autoplay'] = ($ret['row']->autoplay) ? 1 : 0;
        return $ret;
    }

    public function hasContent()
    {
        return !!$this->getRow()->url;
    }
}
