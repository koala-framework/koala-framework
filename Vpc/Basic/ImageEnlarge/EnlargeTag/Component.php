<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_Component extends Vpc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Enlarge Image');
        $ret['alternativePreviewImage'] = true;
        $ret['fullSizeDownloadable'] = false;
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

    private function _getOptions()
    {
        $ret = array();
        if ($this->_getSetting('fullSizeDownloadable')) {
            $row = $this->getImageRow();
            $filename = $row->filename;
            $fRow = $row->getParentRow('Image');
            if (!$fRow) return $ret;
            if (!$filename && $fRow) {
                $filename = $fRow->filename;
            }
            $filename .= '.'.$fRow->extension;
            $ret['fullSizeUrl'] = Vps_Media::getUrl($this->getData()->componentClass,
                $this->getData()->componentId, 'original', $filename);
        }
        return $ret;
    }

    public function getImageRow()
    {
        $d = $this->getData();
        while (!is_instance_of($d->componentClass, 'Vpc_Basic_ImageEnlarge_Component')) {
            $d = $d->parent;
        }
        return $d->getComponent()->getOwnImageRow();
    }

    public function getAlternativePreviewImageRow()
    {
        return parent::getImageRow();
    }

    public static function getMediaOutput($id, $type, $className)
    {
        if ($type == 'original') {
            $row = Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId($id, array('limit'=>1))
                ->getComponent()->getImageRow();
            if (!$row->imageExists()) {
                return null;
            }
            $fileRow = $row->getParentRow('Image');
            return array(
                'file' => $fileRow->getFileSource(),
                'mimeType' => $fileRow->mime_type
            );
        } else {
            return parent::getMediaOutput($id, $type, $className);
        }
    }
}
