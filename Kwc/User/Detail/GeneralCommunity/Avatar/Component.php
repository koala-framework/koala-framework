<?php
class Kwc_User_Detail_GeneralCommunity_Avatar_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Avatar');
        $ret['dimensions'] = array(
            array('width'=>150, 'height'=>150, 'scale'=>Kwf_Media_Image::SCALE_BESTFIT)
        );
        $ret['generators']['small'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_User_Detail_GeneralCommunity_Avatar_Small_Component'
        );
        $ret['emptyImage'] = 'ghost.jpg';
        return $ret;
    }
}
