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
        $parentComponent = $this->getData()->parent->getComponent();
        $ret = $parentComponent->getVideoDimensions();
        $format = $parentComponent->getRow()->format;
        if ($ret['width'] == '100%') {
            $ret['width'] = $parentComponent->getContentWidth();
        }
        if ($ret['height'] == '100%') {
            if ($format == '16x9') {
                $ret['height'] = ($ret['width'] / 16) * 9;
            } else if ($format == '4x3') {
                $ret['height'] = ($ret['width'] / 4) * 3;
            }
        }
        $ret['scale'] = Kwf_Media_Image::SCALE_CROP;
        return $ret;
    }
}
