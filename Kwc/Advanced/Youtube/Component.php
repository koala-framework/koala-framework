<?php
class Kwc_Advanced_Youtube_Component extends Kwc_Abstract_Composite_Component
{
    const REGEX = '/(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/';

    const USER_SELECT = 'user';
    const CONTENT_WIDTH = 'contentWidth';

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Youtube');
        $ret['componentCategory'] = 'media';
        $ret['componentPriority'] = 40;
        $ret['componentIcon'] = 'control_play';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
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

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['config'] = $this->getConfig();
        return $ret;
    }

    public function getConfig()
    {
        $row = $this->_getRow();

        if (preg_match(self::REGEX, $row->url, $matches)) {
            $videoId = $matches[0];
        } else {
            $videoId = null;
        }

        $width = $this->_getSetting('videoWidth');
        if ($width === self::USER_SELECT) {
            if ($row->size == 'fullWidth') {
                $width = null;
            } else {
                $width = (int)$row->video_width;
            }
        } else if ($width === self::CONTENT_WIDTH) {
            $width = null;
        }

        $config = array(
            'videoId' => $videoId,
            'size' => $row->size,
            'width' => $width,
            'height' => null,
            'ratio' => '16x9',
            'resumeOnShow' => true //True is set because the behavior was like that before this option was added
        );
        if ($d = $row->dimensions) {
            if ($d == '16x9') {
                if ($config['width']) $config['height'] = ($config['width'] / 16) * 9;
                $config['ratio'] = self::getBemClass($this, '--ratio'.$d);
            } else if ($d == '4x3') {
                if ($config['width']) $config['height'] = ($config['width'] / 4) * 3;
                $config['ratio'] = self::getBemClass($this, '--ratio'.$d);
            }
        }
        $config = array_merge($config, array('playerVars' => $this->_getSetting('playerVars')));
        $config['playerVars']['autoplay'] = ($row->autoplay) ? 1 : 0;
        return $config;
    }

    public function hasContent()
    {
        return !!$this->getRow()->url;
    }
}
