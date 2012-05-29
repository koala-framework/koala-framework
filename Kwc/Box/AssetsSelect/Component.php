<?php
class Kwc_Box_AssetsSelect_Component extends Kwc_Box_Assets_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['componentName'] = trlKwfStatic('Assets Select');
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'section';
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
