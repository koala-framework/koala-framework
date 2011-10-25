<?php
class Kwf_Component_Generator_Indirect_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        unset($ret['generators']['box']);
        $ret['generators']['static'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_Indirect_Flag'
        );
        return $ret;
    }
}
?>