<?php
class Vps_Component_Cache_Chained_Master_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = new Vps_Component_Cache_Chained_Master_Model();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['text'] = $this->getOwnModel()->getRow($this->getData()->componentId)->value;
        return $ret;
    }
}
