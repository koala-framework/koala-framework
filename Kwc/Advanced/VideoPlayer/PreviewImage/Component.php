<?php
class Kwc_Advanced_VideoPlayer_PreviewImage_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Preview image');
        $ret['dimensions'] = array(
            'default' => array(
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'cover' => true
            )
        );
        return $ret;
    }

    public function getConfiguredImageDimensions()
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
        $ret['cover'] = true;
        return $ret;
    }
}
