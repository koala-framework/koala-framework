<?php
class Kwf_Component_Generator_TwoComponentsWithSamePlugin_Static0 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static1'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_TwoComponentsWithSamePlugin_Static1',
        );
        $ret['generators']['static2'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_TwoComponentsWithSamePlugin_Static2',
        );
        return $ret;
    }

}
