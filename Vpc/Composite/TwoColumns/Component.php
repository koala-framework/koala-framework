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
        return $ret;
    }
}
