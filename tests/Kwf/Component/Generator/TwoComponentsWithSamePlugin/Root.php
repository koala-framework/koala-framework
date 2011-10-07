<?php
class Kwf_Component_Generator_TwoComponentsWithSamePlugin_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['static0'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_TwoComponentsWithSamePlugin_Static0',
        );
        return $ret;
    }
}
?>