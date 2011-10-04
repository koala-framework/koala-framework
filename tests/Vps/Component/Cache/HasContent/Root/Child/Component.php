<?php
class Vps_Component_Cache_HasContent_Root_Child_Component extends Vpc_Basic_Empty_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vps_Component_Cache_HasContent_Root_Child_Model';
        return $ret;
    }

    public function hasContent()
    {
        return $this->getRow()->has_content;
    }
}
