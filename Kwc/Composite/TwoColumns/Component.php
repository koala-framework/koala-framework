<?php
class Vpc_Composite_TwoColumns_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('2 Columns');
        $ret['componentIcon'] = new Vps_Asset('application_tile_horizontal');
        $ret['generators']['child']['component']['leftColumn'] = 'Vpc_Composite_TwoColumns_Left_Component';
        $ret['generators']['child']['component']['rightColumn'] = 'Vpc_Composite_TwoColumns_Right_Component';

        $ret['extConfig'] = 'Vpc_Abstract_Composite_ExtConfigTabs';

        $ret['contentMargin'] = 20;

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['leftWidth'] = $this->_getChildContentWidth($this->getData()->getChildComponent('-leftColumn'));
        $ret['rightWidth'] = $this->_getChildContentWidth($this->getData()->getChildComponent('-rightColumn'));
        return $ret;
    }

    protected function _getChildContentWidth(Vps_Component_Data $child)
    {
        $ret = parent::_getChildContentWidth($child);
        $ret -= $this->_getSetting('contentMargin') * 1;
        $ret = $ret / 2;
        return $ret;
    }

    //TODO: cache meta für breite geändert
}
