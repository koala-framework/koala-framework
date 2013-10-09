<?php
class Kwc_Advanced_VideoPlayer_PreviewImage_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Preview image');
        $ret['dimensions'] = array(
            'original'=>array(
                'text' => trlKwf('original'),
                'width' => 0,
                'height' => 0,
                'scale' => Kwf_Media_Image::SCALE_ORIGINAL
            )
        );
        return $ret;
    }

    protected function _getImageDimensions()
    {
        $ret = $this->getData()->parent->getComponent()->getVideoDimensions();
        $ret['scale'] = Kwf_Media_Image::SCALE_CROP;
        return $ret;
    }
}
