<?php
class Kwc_Posts_Detail_Delete_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['confirmed'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Posts_Detail_Delete_Confirmed_Component',
            'name' => trlKwfStatic('confirmed')
        );
        $ret['placeholder']['deletePost'] = trlKwfStatic('Do you really want to delete this post?');
        $ret['cssClass'] = 'kwfup-webStandard';
        return $ret;
    }

    //hier keine berechtigungsüberpüfung notwendig da nichts gelöscht wird
    //ist in der confirmed komponente
}
