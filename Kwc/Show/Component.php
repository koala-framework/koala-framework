<?php
class Kwc_Show_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Show Component');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
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
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->getRow()->target);
    }
}
