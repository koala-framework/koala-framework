<?php
class Kwc_Advanced_VideoPlayer_PreviewImage_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Preview image');
        $ret['dimensions'] = array();
        return $ret;
    }

    // Deactivated this because no dimensions are set
    public static function validateSettings($settings, $componentClass)
    {
    }

    protected function _getImageDimensions()
    {
        $ret = $this->getData()->parent->getComponent()->getVideoDimensions();
        $ret['scale'] = Kwf_Media_Image::SCALE_CROP;
        return $ret;
    }
}
