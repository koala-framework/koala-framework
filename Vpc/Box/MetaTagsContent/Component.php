<?php
class Vpc_Box_MetaTagsContent_Component extends Vpc_Box_MetaTags_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Meta Tags');
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
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
