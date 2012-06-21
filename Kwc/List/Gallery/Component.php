<?php
class Kwc_List_Gallery_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['dep'][] = 'KwfEnlargeNextPrevious';
        $ret['componentName'] = trlKwfStatic('Gallery');
        $ret['componentIcon'] = new Kwf_Asset('images.png');
        $ret['generators']['child']['component'] = 'Kwc_List_Gallery_Image_Component';
        $ret['cssClass'] = 'webStandard';

        $ret['ownModel'] = 'Kwf_Component_FieldModel';

        $ret['extConfig'] = 'Kwc_List_Gallery_ExtConfig';

        $ret['contentMargin'] = 10;

        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (Kwc_Abstract::hasSetting($componentClass, 'dimensions')) {
            throw new Kwf_Exception("Setting 'dimensions' must NOT exist");
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imagesPerLine'] = $this->_getRow()->columns;
        if (!$ret['imagesPerLine']) $ret['imagesPerLine'] = 1;
        return $ret;
    }

    protected function _getChildContentWidth(Kwf_Component_Data $child)
    {
        $ownWidth = parent::_getChildContentWidth($child);
        $columns = (int)$this->_getRow()->columns;
        if (!$columns) $columns = 1;
        $ownWidth -= ($columns-1) * $this->_getSetting('contentMargin');
        $ret = (int)floor($ownWidth / $columns);
        return $ret;
    }

    //TODO: cache meta für breite geändert
}
