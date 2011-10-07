<?php
class Kwc_Box_MetaTagsContent_Component extends Kwc_Box_MetaTags_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Meta Tags');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }
    protected function _getMetaTags()
    {
        $ret = parent::_getMetaTags();
        $row = $this->_getRow();
        if ($row->keywords) $ret['keywords'] = $row->keywords;
        if ($row->description) $ret['description'] = $row->description;
        return $ret;
    }
}
