<?php
class Kwc_Basic_BackgroundWindowWidth_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Background Image').' ('.trlKwf('Optional').')';
        $ret['dimensions'] = array(
            'fullWidth'=>array(
                'text' => trlKwf('full width'),
                'width' => 2560,
                'height' => 0,
                'cover' => true
            )
        );
        $ret['altText'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imageUrl'] = $this->getImageUrl();
        return $ret;
    }
}

