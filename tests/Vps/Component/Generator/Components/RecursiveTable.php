<?php
class Vps_Component_Generator_Components_RecursiveTable extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Image_Component'
        );
        $ret['generators']['staticpage'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Html_Component'
        );
        return $ret;
    }
}
?>