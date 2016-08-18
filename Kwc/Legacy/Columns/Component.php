<?php
class Kwc_Legacy_Columns_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Kwc_Paragraphs_Component';
        $ret['componentName'] = trlKwfStatic('Columns');
        $ret['componentIcon'] = 'application_tile_horizontal';

        $ret['extConfig'] = 'Kwc_Legacy_Columns_ExtConfig';

        $ret['contentMargin'] = 20;

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        foreach($ret['listItems'] as $k => $v) {
            $ret['listItems'][$k]['width'] = $this->getChildContentWidth($v['data']).'px';
        }
        return $ret;
    }
}
