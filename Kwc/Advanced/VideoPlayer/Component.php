<?php
//add dependency bower mediaelement to use this component
//TODO move this into own repository
class Kwc_Advanced_VideoPlayer_Component extends Kwc_Abstract_Composite_Component
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret = array_merge(parent::getSettings($param), array(
            'ownModel'     => 'Kwc_Advanced_VideoPlayer_Model',
            'componentName' => trlKwfStatic('Video'),
            'componentCategory' => 'media',
            'componentIcon' => 'film',
            'extConfig' => 'Kwf_Component_Abstract_ExtConfig_Form'
        ));
        $ret['assetsAdmin']['dep'][] = 'KwfFormCards';
        $ret['assetsAdmin']['dep'][] = 'KwfFormFile';

        $ret['generators']['child']['component']['previewImage'] = 'Kwc_Advanced_VideoPlayer_PreviewImage_Component';

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

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
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
        $ret['config'] = $this->_getSetting('video');
        $dimensions = $this->getVideoDimensions();
        $ret['config']['videoWidth'] = $dimensions['width'];
        $ret['config']['videoHeight'] = $dimensions['height'];
        $row = $this->getRow();
        if ($row->auto_play) {
            $ret['config']['autoPlay'] = true;
        }
        if ($row->loop) {
            $ret['config']['loop'] = true;
        }

        $ret['imageUrl'] = false;
        if ($image = $this->getData()->getChildComponent('-previewImage')) {
            $ret['imageUrl'] = $image->getComponent()->getImageUrl();
        }

        $ret['rootElementClass'] .= ' format' . $row->format;

        return $ret;
    }

    public function getContentWidth()
    {
        $dimensions = $this->getVideoDimensions();
        if ($dimensions['width'] == '100%') {
            return parent::getContentWidth();
        }
        return $dimensions['width'];
    }

    public function getVideoDimensions()
    {
        $ret = array(
            'width' => 0,
            'height' => 0,
        );

        $videoSetting = $this->_getSetting('video');
        $row = $this->getRow();
        if ($row->size == 'contentWidth') {
            $ret['width'] = '100%';
            $ret['height'] = '100%';
        } else {
            if ($row->video_width) {
                $ret['width'] = $row->video_width;
            } else {
                $ret['width'] = $videoSetting['defaultVideoWidth'];
            }
            if ($row->video_height) {
                $ret['height'] = $row->video_height;
            } else {
                $ret['height'] = $videoSetting['defaultVideoHeight'];
            }
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
        $row = $this->getRow();
        if ($row->source_type == 'links') {
            return $row->{$format.'_url'};
        }
        $ret = Kwf_Media::getUrl($this->getData()->componentClass,
            $this->getData()->componentId, $format, 'video.'.$format);
        $ev = new Kwf_Component_Event_CreateMediaUrl($this->getData()->componentClass, $this->getData(), $ret);
        Kwf_Events_Dispatcher::fireEvent($ev);
        return $ev->url;
    }
}
