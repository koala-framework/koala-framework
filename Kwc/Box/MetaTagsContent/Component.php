<?php
class Kwc_Box_MetaTagsContent_Component extends Kwc_Box_MetaTags_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Meta Tags');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['generators']['child']['component']['ogImage'] = 'Kwc_Box_MetaTagsContent_OpenGraphImage_Component';
        return $ret;
    }
    protected function _getMetaTags()
    {
        $ret = parent::_getMetaTags();
        $row = $this->_getRow();
        if ($row->description) $ret['description'] = $row->description;
        if ($row->og_title) $ret['og:title'] = $row->og_title;
        if ($row->og_description) $ret['og:description'] = $row->og_description;
        $ret['og:url'] = $this->getData()->getPage()->getAbsoluteUrl();
        return $ret;
    }
}
