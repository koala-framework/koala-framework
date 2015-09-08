<?php
class Kwc_List_Gallery_Component extends Kwc_List_Images_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Gallery');
        $ret['componentIcon'] = 'images.png';
        $ret['componentCategory'] = 'content';
        $ret['componentPriority'] = 45;
        $ret['generators']['child']['component'] = 'Kwc_List_Gallery_Image_Component';
        $ret['extConfig'] = 'Kwc_List_Gallery_ExtConfig';
        $ret['placeholder']['moreButton'] = trlKwfStatic('more');
        $ret['breakpoint'] = '600';
        $ret['showMoreLink'] = true;
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
        $showPics = null;
        if ($this->_getSetting('showMoreLink')) {
            $showPics = $this->_getRow()->show_pics;
            $ret['moreButtonText'] = $this->_getPlaceholder('moreButton');
            if ($this->_getRow()->show_more_link_text) $ret['moreButtonText'] = $this->_getRow()->show_more_link_text;
        }
        $ret['cssClass'] .= ' col'.$this->_getGalleryColumns();
        $ret['imagesPerLine'] = $this->_getGalleryColumns();
        if (!$ret['imagesPerLine']) $ret['imagesPerLine'] = 1;
        $ret['downloadAll'] = $this->getData()->getChildComponent('-downloadAll');

        if (($this->_getGalleryColumns() <= $showPics || $ret['imagesPerLine'] >= $showPics) && count($ret['children']) <= $showPics) {
            $ret['showPics'] = null;
        } else {
            $ret['showPics'] = $showPics;
        }
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
