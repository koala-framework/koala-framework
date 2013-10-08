<?php
class Kwc_Box_Favicon_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Favicon');
        $ret['dimensions'] = array(
            array('width'=>0, 'height'=>0, 'cover' => true)
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
