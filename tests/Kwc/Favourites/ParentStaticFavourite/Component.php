<?php
class Kwc_Favourites_ParentStaticFavourite_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['viewCache'] = false;
        $ret['generators']['favourite'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Favourites_Favourite_Component',
            'name' => 'Favourites'
        );
        return $ret;
    }
}
