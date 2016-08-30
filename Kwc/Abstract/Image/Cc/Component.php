<?php
class Kwc_Abstract_Image_Cc_Component extends Kwc_Abstract_Composite_Cc_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        return $ret;
    }

    public function hasContent()
    {
        return $this->getData()->chained->hasContent();
    }
}
