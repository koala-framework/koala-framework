<?php
class Kwc_Composite_TwoColumns_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('2 Columns');
        $ret['componentIcon'] = 'application_tile_horizontal';
        $ret['generators']['child']['component']['leftColumn'] = 'Kwc_Composite_TwoColumns_Left_Component';
        $ret['generators']['child']['component']['rightColumn'] = 'Kwc_Composite_TwoColumns_Right_Component';

        $ret['extConfig'] = 'Kwc_Abstract_Composite_ExtConfigTabs';

        $ret['contentMargin'] = 20;

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['leftWidth'] = $this->_getChildContentWidth($this->getData()->getChildComponent('-leftColumn'));
        $ret['rightWidth'] = $this->_getChildContentWidth($this->getData()->getChildComponent('-rightColumn'));
        return $ret;
    }

    protected function _getChildContentWidth(Kwf_Component_Data $child)
    {
        $ret = parent::_getChildContentWidth($child);
        $ret -= $this->_getSetting('contentMargin') * 1;
        $ret = $ret / 2;
        return $ret;
    }
}
