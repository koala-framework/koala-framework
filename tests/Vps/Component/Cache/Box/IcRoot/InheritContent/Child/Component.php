<?php
class Vps_Component_Cache_Box_IcRoot_InheritContent_Child_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vps_Component_Cache_Box_IcRoot_InheritContent_Child_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->getRow()->content;
        return $ret;
    }

    public function hasContent()
    {
        return $this->getRow()->content != null;
    }
}
