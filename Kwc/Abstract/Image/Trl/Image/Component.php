<?php
class Kwc_Abstract_Image_Trl_Image_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings($masterImageComponentClass)
    {
        $ret = parent::getSettings($masterImageComponentClass);
        $ret['ownModel'] = 'Kwc_Abstract_Image_Trl_Image_Model';
        //dimesion wird autom. vom master verwendet
        $ret['masterImageComponentClass'] = $masterImageComponentClass;
        $ret['editFilename'] = false;
        $ret['altText'] = false;
        $ret['titleText'] = false;
        return $ret;
    }

    public function getConfiguredImageDimensions()
    {
        $dimension = $this->getData()->parent->chained->getComponent()->getConfiguredImageDimensions();
        $row = $this->getRow();
        if ($row->crop_width && $row->crop_height) {
            $dimension['crop']['x'] = $row->crop_x;
            $dimension['crop']['y'] = $row->crop_y;
            $dimension['crop']['width'] = $row->crop_width;
            $dimension['crop']['height'] = $row->crop_height;
        } else {
            unset($dimension['crop']);
        }
        return $dimension;
    }

    public function getImageData()
    {
        $ret = parent::getImageData();
        if ($ret && (Kwc_Abstract::getSetting($this->_getSetting('masterImageComponentClass'), 'editFilename'))) {
            $fn = $this->getData()->parent->getComponent()->getRow()->filename;
            if (!$fn) {
                $fn = $this->getData()->parent->chained->getComponent()->getRow()->filename;
            }
            if ($fn) {
                $fileRow = $this->getRow()->getParentRow('Image');
                $ret['filename'] = $fn.'.'.$fileRow->extension;
            }
        }
        return $ret;
    }
}
