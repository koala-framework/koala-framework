<?php
class Kwc_Box_BackgroundImage_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Background Image');
        $ret['dimensions'] = array(
            'original'=>array(
                'text' => trlKwfStatic('Original'),
                'width' => 0,
                'height' => 0,
                'cover' => true
            ),
        );
        $ret['assets']['dep'][] = 'ModernizrCssBackgroundsizecover';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imageUrl'] = $this->getImageUrl();
        return $ret;
    }
}
