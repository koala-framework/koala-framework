<?php
class Vpc_User_Detail_GeneralCommunity_Avatar_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Avatar');
        $ret['dimensions'] = array(
            array('width'=>150, 'height'=>150, 'scale'=>Vps_Media_Image::SCALE_BESTFIT)
        );
        $ret['generators']['small'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_User_Detail_GeneralCommunity_Avatar_Small_Component'
        );
        $ret['emptyImage'] = 'ghost.jpg';
        return $ret;
    }
}
