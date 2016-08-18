<?php
class Kwc_Basic_BackgroundWindowWidth_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Background Image').' ('.trlKwfStatic('Optional').')';
        $ret['dimensions'] = array(
            'fullWidth'=>array(
                'text' => trlKwfStatic('full width'),
                'width' => 2560,
                'height' => 0,
                'cover' => true
            )
        );
        $ret['altText'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['imageUrl'] = $this->getImageUrl();
        return $ret;
    }
}

