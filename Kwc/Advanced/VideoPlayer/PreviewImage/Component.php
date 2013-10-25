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
        $row = $parentComponent->getRow();
        if ($row->size == 'contentWidth') {
            $ret['width'] = $parentComponent->getContentWidth();
            if ($row->format == '4x3') {
                $ret['height'] = (int)(($ret['width'] / 4) * 3);
            } else {
                $ret['height'] = (int)(($ret['width'] / 16) * 9);
            }
        }
        $ret['scale'] = Kwf_Media_Image::SCALE_CROP;
        return $ret;
    }
}
