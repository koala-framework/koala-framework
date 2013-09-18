<?php
class Kwc_Advanced_VideoPlayer_Component extends Kwc_Abstract_Composite_Component
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Videoplayer';
        $ret = array_merge(parent::getSettings(), array(
            'ownModel'     => 'Kwc_Advanced_VideoPlayer_Model',
            'componentName' => trlKwfStatic('Video'),
            'componentIcon' => new Kwf_Asset('film'),
            'extConfig' => 'Kwf_Component_Abstract_ExtConfig_Form'
        ));
        $ret['assets']['files'][] = 'kwf/Kwc/Advanced/VideoPlayer/Component.js';
        $ret['assetsAdmin']['dep'][] = 'KwfFormFile';
        $ret['assets']['dep'][] = 'jQuery';
        $ret['assets']['dep'][] = 'mediaelement';
        $ret['assets']['dep'][] = 'KwfOnReady';

        $ret['video'] = array(
            'defaultVideoWidth' =>  480,
            'defaultVideoHeight' => 270,
            'videoWidth' => -1,
            'videoHeight' => -1,
            'audioWidth' => 400,
            'audioHeight' => 30,
            'startVolume' => 0.8,
            'loop' => false,
            'enableAutosize' => true,
            'features' => array(
                'playpause','progress','current','duration','tracks','volume','fullscreen'
                ),
            'alwaysShowControls' => false,
            'iPadUseNativeControls' => false,
            'iPhoneUseNativeControls' => false,
            'AndroidUseNativeControls' => false,
            'alwaysShowHours' => false,
            'showTimecodeFrameCount' => false,
            'framesPerSecond' => 25,
            'enableKeyboard' => true,
            'pauseOtherPlayers' => true,
            'keyActions' => array(),
            'autoPlay' => false
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['sources'] = array();

        //mp4
        $url = $this->_getVideoUrl('mp4');
        if ($url) {
            $ret['sources'][] = array(
                'src' => $url,
                'type' => 'video/mp4',
                'title' => 'mp4',
            );
        }

        //webm
        $url = $this->_getVideoUrl('webm');
        if ($url) {
            $ret['sources'][] = array(
                'src' => $url,
                'type' => 'video/webm',
                'title' => 'webm',
            );
        }

        //ogg
        $url = $this->_getVideoUrl('ogg');
        if ($url) {
            $ret['sources'][] = array(
                'src' => $url,
                'type' => 'video/ogg',
                'title' => 'ogg',
            );
        }
        $videoSetting = $this->getSetting($this->getData()->getComponentClass(), 'video');
        $ret['config'] = Kwc_Abstract::getSetting($this->getData()->componentClass, 'video');
        $row = $this->getRow();
        if ($row->video_width) {
            $ret['config']['videoWidth'] = $row->video_width;
        } else {
            $ret['config']['videoWidth'] = $videoSetting['defaultVideoWidth'];
        }
        if ($row->video_height) {
            $ret['config']['videoHeight'] = $row->video_height;
        } else {
            $ret['config']['videoHeight'] = $videoSetting['defaultVideoHeight'];
        }
        if ($row->auto_play) {
            $ret['config']['autoPlay'] = true;
        }
        if ($row->loop) {
            $ret['config']['loop'] = true;
        }
        return $ret;
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValid($id);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $s = new Kwf_Component_Select();
        $s->ignoreVisible(true);
        $component =Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, $s);
        $row = $component->getComponent()->getRow();
        if ($type == 'webm') {
            $uploadRow = $row->getParentRow('FileWebm');
            $mimeType = 'video/webm';
        } else if ($type == 'mp4') {
            $uploadRow = $row->getParentRow('FileMp4');
            $mimeType = 'video/mp4';
        } else if ($type == 'ogg') {
            $uploadRow = $row->getParentRow('FileOgg');
            $mimeType = 'video/ogg';
        }
        if (!$uploadRow) return null;
        return array(
            'file'=>$uploadRow->getFileSource(),
            'mimeType' => $mimeType
        );
    }

    protected function _getVideoUrl($format = 'mp4')
    {
        return Kwf_Media::getUrl($this->getData()->componentClass,
            $this->getData()->componentId, $format, 'video.'.$format);
    }
}
