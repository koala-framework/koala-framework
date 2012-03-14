<?php
class Kwc_List_Gallery_Image_Component extends Kwc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Image');
        $ret['generators']['child']['component']['linkTag'] =
            'Kwc_List_Gallery_Image_LinkTag_Component';
        $ret['imageCaption'] = true;

        $ret['dimensions'] = array(
            'fullWidth'=>array(
                'text' => trlKwf('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'scale' => Kwf_Media_Image::SCALE_DEFORM
            ),
        );

        $ret['aspectRatio'] = 3/4;
        return $ret;
    }

    protected function _calculateResultingImageDimensions($size)
    {
        if ($this->_getSetting('aspectRatio')) {
            $size['height'] = $size['width'] * $this->_getSetting('aspectRatio');
            $size['scale'] = Kwf_Media_Image::SCALE_CROP;
        }
        return parent::_calculateResultingImageDimensions($size);
    }
}
