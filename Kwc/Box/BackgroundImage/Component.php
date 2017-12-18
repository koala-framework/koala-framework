<?php
class Kwc_Box_BackgroundImage_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Background Image');
        $ret['dimensions'] = array(
            'original'=>array(
                'text' => trlKwfStatic('Original'),
                'width' => 0,
                'height' => 0,
                'cover' => true
            ),
        );
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['imageUrl'] = $this->getImageUrl();
        return $ret;
    }
}
