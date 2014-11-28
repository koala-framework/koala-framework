<?php
class Kwc_Trl_ImageEnlarge_Master extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        // de Bild
        $ret['generators']['test1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test1',
        );
        // de Bild + Vorschau
        $ret['generators']['test2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test2',
        );
        // de Bild | en Bild
        $ret['generators']['test3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test3',
        );
        // de Bild + Vorschau | en Bild
        $ret['generators']['test4'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test4',
        );
        // de Bild + Vorschau | en Bild + Vorschau
        $ret['generators']['test5'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test5',
        );
        // de Bild + Vorschau | en nur Vorschau
        $ret['generators']['test6'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test6',
        );
        $ret['generators']['test7'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_ImageEnlarge_ImageEnlarge_Component',
            'name' => 'test7',
        );
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
