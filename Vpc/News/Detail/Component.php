<?php
class Vpc_News_Detail_Component extends Vpc_News_Detail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['image'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_News_Detail_PreviewImage_Component'
        );
        return $ret;
    }
}
