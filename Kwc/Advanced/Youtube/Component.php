<?php
class Kwc_Advanced_Youtube_Component extends Kwc_Abstract
{
    const REGEX = '/(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/';

    const USER_SELECT = 'user';
    const CONTENT_WIDTH = 'contentWidth';

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Youtube');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['assets']['dep'][] = 'KwfYoutubePlayer';

        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';

        $ret['videoWidth'] = self::USER_SELECT;
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
        $width = $this->_getSetting('videoWidth');
        if ($width === self::USER_SELECT) {
            $width = (int)$ret['row']->videoWidth;
        } else if (is_int($width)) {
            $width = $width;
        }
        if (!$width || $width === self::CONTENT_WIDTH) {
            $width = (int)$this->getContentWidth();
        }
        $config = array(
            'videoId' => $matches[0],
            'width' => $width,
            'height' => 0
        );
        if ($d = $ret['row']->dimensions) {
            if ($d == '16x9') {
                $config['height'] = ($config['width'] / 16) * 9;
                $config['ratio'] = $d;
            } else if ($d == '4x3') {
                $config['height'] = ($config['width'] / 4) * 3;
                $config['ratio'] = $d;
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
