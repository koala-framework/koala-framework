<?php
class Vps_Component_Cache_OwnModel_Root_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vps_Component_Cache_OwnModel_Root_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->getRow()->content;
        return $ret;
    }
}
