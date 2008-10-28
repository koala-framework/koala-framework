<?php
class Vpc_Basic_Image_Thumb_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(100, 75, Vps_Media_Image::SCALE_CROP);
        return $ret;
    }

    public function getImageRow()
    {
        $ret = false;
        if (Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'hasSmallImageComponent')) {
            $ret = $this->_getRow();
        }
        if (!$ret || !$ret->imageExists()) {
            $ret = $this->getModel()->getRow($this->getData()->parent->dbId);
        }
        return $ret;
    }
}