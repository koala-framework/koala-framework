<?php
class Kwf_Component_Cache_Composite_Root_C1_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_Cache_Composite_Root_C1_Model';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = array('has_content');
        return $ret;
    }

    public function hasContent()
    {
        return $this->getRow()->has_content;
    }
}
