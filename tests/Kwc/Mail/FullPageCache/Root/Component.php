<?php
class Kwc_Mail_FullPageCache_Root_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['testMail1'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Mail_FullPageCache_TestMail_Component',
        );
        $ret['generators']['testMail2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Mail_FullPageCache_TestMail_Component',
        );
        return $ret;
    }
}
