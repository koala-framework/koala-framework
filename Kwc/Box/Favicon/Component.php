<?php
class Kwc_Box_Favicon_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Favicon');
        $ret['dimensions'] = array(
            array('width'=>16, 'height'=>16, 'scale'=>Kwf_Media_Image::SCALE_ORIGINAL)
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imageUrl'] = $this->getImageUrl();
        return $ret;
    }
}
