<?php
class Kwc_List_Gallery_Component extends Kwc_List_Images_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Gallery');
        $ret['componentIcon'] = new Kwf_Asset('images.png');
        $ret['generators']['child']['component'] = 'Kwc_List_Gallery_Image_Component';
        $ret['extConfig'] = 'Kwc_List_Gallery_ExtConfig';
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (Kwc_Abstract::hasSetting($componentClass, 'dimensions')) {
            throw new Kwf_Exception("Setting 'dimensions' must NOT exist");
        }
    }

    protected function _getGalleryColumns()
    {
        return $this->_getRow()->columns;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imagesPerLine'] = $this->_getGalleryColumns();
        if (!$ret['imagesPerLine']) $ret['imagesPerLine'] = 1;
        $ret['downloadAll'] = $this->getData()->getChildComponent('-downloadAll');
        return $ret;
    }

    protected function _getChildContentWidth(Kwf_Component_Data $child)
    {
        $ownWidth = parent::_getChildContentWidth($child);
        $columns = (int)$this->_getGalleryColumns();
        if (!$columns) $columns = 1;
        $ownWidth -= ($columns-1) * $this->_getSetting('contentMargin');
        $ret = (int)floor($ownWidth / $columns);
        return $ret;
    }
}
