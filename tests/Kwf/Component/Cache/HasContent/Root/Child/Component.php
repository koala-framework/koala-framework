<?php
class Kwf_Component_Cache_HasContent_Root_Child_Component extends Kwc_Basic_Empty_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_Cache_HasContent_Root_Child_Model';
        return $ret;
    }

    public function hasContent()
    {
        return $this->getRow()->has_content;
    }
}
