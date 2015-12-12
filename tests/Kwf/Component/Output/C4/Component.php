<?php
class Kwf_Component_Output_C4_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentWidth'] = 600;
        return $ret;
    }

    public function getViewCacheLifetime()
    {
        return 3;
    }
}
?>
