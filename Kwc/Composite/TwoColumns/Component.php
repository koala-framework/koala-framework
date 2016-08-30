<?php
class Kwc_Composite_TwoColumns_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('2 Columns');
        $ret['componentIcon'] = 'application_tile_horizontal';
        $ret['generators']['child']['component']['leftColumn'] = 'Kwc_Composite_TwoColumns_Left_Component';
        $ret['generators']['child']['component']['rightColumn'] = 'Kwc_Composite_TwoColumns_Right_Component';

        $ret['extConfig'] = 'Kwc_Abstract_Composite_ExtConfigTabs';
        $ret['layoutClass'] = 'Kwc_Composite_TwoColumns_Layout';

        $ret['contentMargin'] = 20;

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['leftWidth'] = $this->_getChildContentWidth($this->getData()->getChildComponent('-leftColumn'));
        $ret['rightWidth'] = $this->_getChildContentWidth($this->getData()->getChildComponent('-rightColumn'));
        return $ret;
    }
}
