<?php
class Kwf_Component_Cache_ParentContent_RootBoxSelect_Box_Component extends Kwc_Basic_Empty_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'has_content';
        $ret['ownModel'] = 'Kwf_Component_Cache_ParentContent_RootBoxSelect_Box_Model';
        return $ret;
    }

    public function hasContent()
    {
        return $this->getRow()->has_content;
    }
}
