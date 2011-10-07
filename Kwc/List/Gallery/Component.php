<?php
abstract class Vpc_List_Gallery_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['dep'][] = 'VpsEnlargeNextPrevious';
        $ret['componentName'] = trlVps('Gallery');
        $ret['componentIcon'] = new Vps_Asset('images.png');
        $ret['generators']['child']['component'] = 'Vpc_List_Gallery_Image_Component';
        $ret['cssClass'] = 'webStandard';

        $ret['ownModel'] = 'Vps_Component_FieldModel';

        $ret['extConfig'] = 'Vpc_List_Gallery_ExtConfig';

        $ret['contentMargin'] = 10;

        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (Vpc_Abstract::hasSetting($componentClass, 'dimensions')) {
            throw new Vps_Exception("Setting 'dimensions' must NOT exist");
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imagesPerLine'] = $this->_getRow()->columns;
        if (!$ret['imagesPerLine']) $ret['imagesPerLine'] = 1;
        return $ret;
    }

    protected function _getChildContentWidth(Vps_Component_Data $child)
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
