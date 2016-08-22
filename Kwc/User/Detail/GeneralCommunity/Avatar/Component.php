<?php
class Kwc_User_Detail_GeneralCommunity_Avatar_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Avatar');
        $ret['dimensions'] = array(
            array('width'=>150, 'height'=>150, 'cover' => false)
        );
        $ret['generators']['small'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_User_Detail_GeneralCommunity_Avatar_Small_Component'
        );
        $ret['emptyImage'] = 'ghost.jpg';
        return $ret;
    }
}
