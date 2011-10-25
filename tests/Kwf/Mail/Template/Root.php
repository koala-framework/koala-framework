<?php

class Kwf_Mail_Template_Root extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']);
        $ret['generators']['both'] = array(
            'component' => 'Kwf_Mail_Template_Both_Component',
            'class' => 'Kwf_Component_Generator_Static'
        );
        $ret['generators']['notpl'] = array(
            'component' => 'Kwf_Mail_Template_NoTpl_Component',
            'class' => 'Kwf_Component_Generator_Static'
        );
        $ret['generators']['htmlonly'] = array(
            'component' => 'Kwf_Mail_Template_HtmlOnly_Component',
            'class' => 'Kwf_Component_Generator_Static'
        );
        $ret['generators']['txtonly'] = array(
            'component' => 'Kwf_Mail_Template_TxtOnly_Component',
            'class' => 'Kwf_Component_Generator_Static'
        );
        return $ret;
    }
}
