<?php
class Vpc_Trl_ImageEnlarge_Master extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        // de Bild
        $ret['generators']['test1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test1',
        );
        // de Bild + Vorschau
        $ret['generators']['test2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test2',
        );
        // de Bild | en Bild
        $ret['generators']['test3'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test3',
        );
        // de Bild + Vorschau | en Bild
        $ret['generators']['test4'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test4',
        );
        // de Bild + Vorschau | en Bild + Vorschau
        $ret['generators']['test5'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test5',
        );
        // de Bild + Vorschau | en nur Vorschau
        $ret['generators']['test6'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test6',
        );
        return $ret;
    }
}
