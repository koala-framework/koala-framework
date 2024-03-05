<?php
class Kwc_List_Gallery_Component extends Kwc_List_Images_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Gallery');
        $ret['componentIcon'] = 'images.png';
        $ret['componentCategory'] = 'media';
        $ret['componentPriority'] = 45;
        $ret['generators']['child']['component'] = 'Kwc_List_Gallery_Image_Component';
        $ret['extConfig'] = 'Kwc_List_Gallery_ExtConfig';
        $ret['layoutClass'] = 'Kwc_List_Gallery_Layout';
        $ret['placeholder']['moreButton'] = trlKwfStatic('more');
        $ret['breakpoint'] = '600';
        $ret['showMoreLink'] = true;
        $ret['defaultVisible'] = true;
        $ret['apiContent'] = 'Kwc_List_Gallery_ApiContent';
        $ret['apiContentType'] = 'gallery';
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (Kwc_Abstract::hasSetting($componentClass, 'dimensions')) {
            throw new Kwf_Exception("Setting 'dimensions' must NOT exist");
        }
    }

    public final function getGalleryColumns()
    {
        return $this->_getGalleryColumns();
    }

    protected function _getGalleryColumns()
    {
        return $this->_getRow()->columns;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $showPics = null;
        if ($this->_getSetting('showMoreLink')) {
            $showPics = $this->_getRow()->show_pics;
            $ret['moreButtonText'] = $this->_getPlaceholder('moreButton');
            if ($this->_getRow()->show_more_link_text) $ret['moreButtonText'] = $this->_getRow()->show_more_link_text;
        }
        $ret['rootElementClass'] .= ' col'.$this->_getGalleryColumns();

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
}
