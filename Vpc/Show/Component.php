<?php
class Vpc_Show_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Show Component');
        $ret['modelname'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['target'] = $this->getShowComponent();
        return $ret;
    }

    public function getShowComponent()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->getRow()->target);
    }
}
