<?php
class Vpc_Posts_Detail_Delete_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['confirmed'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Detail_Delete_Confirmed_Component',
            'name' => trlVps('confirmed')
        );
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
}
