<?php
class Kwf_Component_Generator_Components_Plugin extends Kwf_Component_Plugin_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['pluginStatic'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Basic_Line_Component'
        );
        return $ret;
    }
}
?>