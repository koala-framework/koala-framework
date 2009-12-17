<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_Component extends Vpc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Enlarge Image');
        $ret['alternativePreviewImage'] = true;
        $ret['fullSizeDownloadable'] = false;
        $ret['imageTitle'] = true;
        $ret['dimensions'] = array(array('width'=>640, 'height'=>480, 'scale'=>Vps_Media_Image::SCALE_BESTFIT));

        $ret['assets']['files'][] = 'vps/Vpc/Basic/ImageEnlarge/EnlargeTag/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['dep'][] = 'ExtXTemplate';
        $ret['assets']['dep'][] = 'ExtUtilJson';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $size = $this->getImageDimensions();
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];

        $ret['imageUrl'] = $this->getImageUrl();

        $ret['options'] = (object)$this->_getOptions();

        return $ret;
    }

    protected function _getOptions()
    {
        $ret = array();
        if ($this->_getSetting('imageTitle')) {
            $ret['title'] = $this->getRow()->title;
        }
        if ($this->_getSetting('fullSizeDownloadable')) {
            $data = $this->getImageData();
            if ($data) {
                $ret['fullSizeUrl'] = Vps_Media::getUrl($this->getData()->componentClass,
                    $this->getData()->componentId, 'original', $data['filename']);
            }
        }
        return $ret;
    }

    public function getImageData()
    {
        $d = $this->getData();
        while (!is_instance_of($d->componentClass, 'Vpc_Basic_ImageEnlarge_Component')) {
            $d = $d->parent;
        }
        return $d->getComponent()->getOwnImageData();
    }

    public function getAlternativePreviewImageData()
    {
        return parent::getImageData();
    }

    public static function getMediaOutput($id, $type, $className)
    {
        if ($type == 'original') {
            $data = Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId($id, array('limit'=>1))
                ->getComponent()->getImageData();
            if (!$data) {
                return null;
            }
            return array(
                'file' => $data['file'],
                'mimeType' => $data['mimeType']
            );
        } else {
            return parent::getMediaOutput($id, $type, $className);
        }
    }
}
