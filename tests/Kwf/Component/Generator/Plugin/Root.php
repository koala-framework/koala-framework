<?php
class Kwf_Component_Generator_Plugin_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['static'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_Plugin_Static',
            'name' => 'Static'
        );
        return $ret;
    }
}
?>