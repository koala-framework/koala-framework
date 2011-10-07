<?php
class Vpc_Trl_Image_Master extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        // de Bild
        $ret['generators']['test1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_Image_Image_Component',
            'name' => 'test1',
        );
        // de Bild | en Bild
        $ret['generators']['test2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_Image_Image_Component',
            'name' => 'test2',
        );
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
