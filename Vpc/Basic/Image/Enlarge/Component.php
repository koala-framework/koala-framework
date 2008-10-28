<?php
class Vpc_Basic_Image_Enlarge_Component extends Vpc_Basic_Image_Component
{
    private $_smallImageComponent;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image Enlarge');
        $ret['componentIcon'] = new Vps_Asset('imageEnlarge');
        $ret['modelname'] = 'Vpc_Basic_Image_Enlarge_Model';
        $ret['hasSmallImageComponent'] = true;
        $ret['fullSizeDownloadable'] = false;
        $ret['generators']['smallImage'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Image_Thumb_Component'
        );
        $ret['dimensions'] = array(640, 480);
        $ret['assets']['files'][] = 'vps/Vpc/Basic/Image/Enlarge/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['dep'][] = 'ExtXTemplate';
        $ret['editComment'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['url'] = $this->getImageUrl();
        $size = $this->getImageDimensions();
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];

        $ret['smallImage'] = $this->getSmallImage();

        $ret['fullSizeUrl'] = false;
        if ($this->_getSetting('fullSizeDownloadable')) {
            $row = $this->getImageRow();
            $filename = $row->filename;
            $fRow = $row->getParentRow('Image');
            if (!$filename && $fRow) {
                $filename = $fRow->filename;
            }
            $filename .= '.'.$fRow->extension;
            $ret['fullSizeUrl'] = Vps_Media::getUrl($this->getData()->componentClass,
                $this->getData()->dbId, 'original', $filename);
        }
        return $ret;
    }

    private function _getSmallImageComponent()
    {
        if (!isset($this->_smallImageComponent)) {
            $this->_smallImageComponent = $this->getData()->getChildComponent('-smallImage')->getComponent();
        }
        return $this->_smallImageComponent;
    }

    //wird uA. verwendet in Vpc_Composite_ImagesEnlarge_Component
    public function getSmallImage()
    {
        $ret = array();
        $ret['row'] = $this->_getSmallImageComponent()->getImageRow();
        $ret['url'] = $this->_getSmallImageComponent()->getImageUrl();
        $size = $this->_getSmallImageComponent()->getImageDimensions();
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];
        return $ret;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        if ($type == 'original') {
            $row = Vpc_Abstract::createModel($className)->getRow($id);
            if ($row) {
                $fileRow = $row->getParentRow('Image');
            } else {
                return null;
            }
            return array(
                'file' => $fileRow->getFileSource(),
                'mimeType' => $fileRow->mime_type
            );
        } else {
            return parent::getMediaOutput($id, $type, $className);
        }
    }
}
