<?php
class Kwc_Trl_LinkTag_German extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_LinkTag_LinkTag_Component',
            'name' => 'test1',
        );
        $ret['generators']['test2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_LinkTag_LinkTag_Component',
            'name' => 'test2',
        );
        $ret['generators']['test3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_LinkTag_LinkTag_Component',
            'name' => 'test3',
        );
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
