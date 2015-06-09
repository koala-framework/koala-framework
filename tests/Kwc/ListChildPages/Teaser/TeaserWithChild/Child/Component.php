<?php
class Kwc_ListChildPages_Teaser_TeaserWithChild_Child_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_ListChildPages_Teaser_TeaserWithChild_Child_Model';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'visible';
//         $ret['throwHasContentChangedOnRowColumnsUpdate'] = true;
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->visible) return true;
        return false;
    }
}
