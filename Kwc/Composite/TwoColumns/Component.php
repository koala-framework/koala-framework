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
        $ret['layoutClass'] = 'Kwc_Composite_TwoColumns_Layout';

        $ret['contentMargin'] = 20;

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['leftWidth'] = $this->getData()->getChildComponent('-leftColumn')->getComponent()->getContentWidth();
        $ret['rightWidth'] = $this->getData()->getChildComponent('-rightColumn')->getComponent()->getContentWidth();
        return $ret;
    }
}
