<?php
class Kwc_Basic_TextMailTxt_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['mail1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_TextMailTxt_Mail_Component',
            'name' => 'mail1',
        );
        $ret['generators']['mail2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_TextMailTxt_Mail_Component',
            'name' => 'mail2',
        );
        return $ret;
    }
}
