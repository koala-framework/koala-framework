<?php
class Kwc_Trl_Image_Master extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        // de Bild
        $ret['generators']['test1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_Image_Image_Component',
            'name' => 'test1',
        );
        // de Bild | en Bild
        $ret['generators']['test2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_Image_Image_Component',
            'name' => 'test2',
        );
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
