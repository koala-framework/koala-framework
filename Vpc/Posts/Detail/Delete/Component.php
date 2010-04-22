<?php
class Vpc_Posts_Detail_Delete_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['confirmed'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Detail_Delete_Confirmed_Component',
            'name' => trlVpsStatic('confirmed')
        );
        $ret['placeholder']['deletePost'] = trlVpsStatic('Do you really want to delete this post?');
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    //hier keine berechtigungsüberpüfung notwendig da nichts gelöscht wird
    //ist in der confirmed komponente
}
