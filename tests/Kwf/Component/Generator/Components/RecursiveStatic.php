<?php
class Kwf_Component_Generator_Components_RecursiveStatic extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwf_Component_Generator_Components_RecursiveStatic'
        );
        return $ret;
    }
}
?>