<?php
class Kwc_Lightbox_DynamicContent_TestComponent_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Lightbox_DynamicContent_TestComponent_TestModel';
        $ret['contentSender'] = 'Kwf_Component_Abstract_ContentSender_Lightbox';
        $ret['lightboxOptions'] = array(
            'adaptHeight' => true,
            'height' => 1336,
            'width' => 2040
        );
        $ret['assets']['dep'][] = 'KwfLightbox';
        $ret['dimensions'] = array(
            'fullWidth'=>array(
                'text' => trlKwf('full width'),
                'cover' => true
            )
        );
        return $ret;
    }

    public function getContentWidth()
    {
        return 2040;
    }
}
