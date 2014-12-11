<?php
class Kwc_List_Gallery_Component extends Kwc_List_Images_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Gallery');
        $ret['componentIcon'] = new Kwf_Asset('images.png');
        $ret['componentCategory'] = 'content';
        $ret['generators']['child']['component'] = 'Kwc_List_Gallery_Image_Component';
        $ret['extConfig'] = 'Kwc_List_Gallery_ExtConfig';
        $ret['breakpoint'] = '600';
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
        $ret['cssClass'] .= ' col'.$this->_getGalleryColumns();
        $ret['imagesPerLine'] = $this->_getGalleryColumns();
        if (!$ret['imagesPerLine']) $ret['imagesPerLine'] = 1;
        $ret['downloadAll'] = $this->getData()->getChildComponent('-downloadAll');
        $ret['showPics'] = $this->_getRow()->show_pics;
        return $ret;
    }

    protected function _getChildContentWidth(Kwf_Component_Data $child)
    {
        $ownWidth = parent::_getChildContentWidth($child);
        $contentMargin = $this->_getSetting('contentMargin');
        $breakpoint = $this->_getSetting('breakpoint');
        $columns = (int)$this->_getGalleryColumns();
        $ownWidth -= ($columns-1) * $contentMargin;

        if (!$columns) $columns = 1;
        if ($columns >=5 && $columns <= 10) {
            $originColumnWidth = (int)floor($ownWidth / $columns);
            if ($columns == 6) {
                $columns = 3;
            }
            if ($columns % 2 == 0) {
                $columns = 4;
            } else {
                $columns = 3;
            }
            $ret = (int)floor((($breakpoint - $contentMargin) - ($columns-1) * $contentMargin) / $columns);
            if ($ret < $originColumnWidth) {
                $ret = $originColumnWidth;
            }
        } else {
            $ret = (int)floor($ownWidth / $columns);
        }
        return $ret;
    }
}
