<?php
class Kwc_Errors_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['contentSender'] = 'Kwc_Errors_ContentSender';
        $ret['generators']['accessDenied'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Errors_AccessDenied_Component',
            'name' => trlKwfStatic('Access denied'),
        );
        $ret['generators']['notFound'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Errors_NotFound_Component',
            'name' => trlKwfStatic('Not found'),
        );
        $ret['flags']['noIndex'] = true;
        $ret['flags']['skipFulltextRecursive'] = true;
        return $ret;
    }
}
