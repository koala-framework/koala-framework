<?php
class Vpc_Box_AssetsSelect_Component extends Vpc_Box_Assets_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['componentName'] = trlVps('Assets Select');
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    protected function _getSection()
    {
        $ret = $this->getRow()->section;
        if (!$ret) $ret = 'web';
        return $ret;
    }
    
    public function hasContent()
    {
        return (bool)$this->getRow()->section;
    }
}
