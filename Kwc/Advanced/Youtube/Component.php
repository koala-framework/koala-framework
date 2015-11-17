<?php
class Kwc_Advanced_Youtube_Component extends Kwc_Abstract_Composite_Component
{
    const REGEX = '/(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/';

    const USER_SELECT = 'user';
    const CONTENT_WIDTH = 'contentWidth';

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Youtube');
        $ret['componentCategory'] = 'content';
        $ret['componentPriority'] = 40;
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['assetsDefer']['dep'][] = 'KwfYoutubePlayer';
        $ret['assetsAdmin']['dep'][] = 'KwfFormCards';

        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';

        $ret['videoWidth'] = self::USER_SELECT;
        $ret['playerVars'] = array(
            'rel' => 0,
            'iv_load_policy' => 3,
            'wmode' => 'opaque',
            'showinfo' => '0'
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

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);

        if (preg_match(self::REGEX, $ret['row']->url, $matches)) {
            $videoId = $matches[0];
        } else {
            $videoId = null;
        }

        $width = $this->_getSetting('videoWidth');
        if ($width === self::USER_SELECT) {
            if ($ret['row']->size == 'fullWidth') {
                $width = null;
            } else {
                $width = (int)$ret['row']->video_width;
            }
        } else if ($width === self::CONTENT_WIDTH) {
            $width = null;
        }
        $config = array(
            'videoId' => $videoId,
            'size' => $ret['row']->size,
            'width' => $width,
            'height' => null,
            'ratio' => '16x9'
        );
        if ($d = $ret['row']->dimensions) {
            if ($d == '16x9') {
                if ($config['width']) $config['height'] = ($config['width'] / 16) * 9;
                $config['ratio'] = $d;
            } else if ($d == '4x3') {
                if ($config['width']) $config['height'] = ($config['width'] / 4) * 3;
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
