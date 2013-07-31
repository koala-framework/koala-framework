<?php
class RedMallee_Box_BackgroundImage_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Hintergrundbild');

        $ret['assets']['files'][] = 'kwf/themes/RedMallee/Box/BackgroundImage/Component.js';
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imageUrl'] = $this->getImageUrl();
        return $ret;
    }
}
