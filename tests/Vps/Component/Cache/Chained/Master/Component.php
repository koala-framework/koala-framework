<?php
class Vps_Component_Cache_Chained_Master_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vps_Component_Cache_Chained_Master_Model';
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['text'] = $this->getRow()->value;
        return $ret;
    }
}
