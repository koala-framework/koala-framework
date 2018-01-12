<?php
class Kwc_User_LostPassword_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['setpass'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_User_LostPassword_SetPassword_Component',
            'name' => trlKwfStatic('Set password')
        );
        $ret['generators']['child']['component']['form'] = 'Kwc_User_LostPassword_Form_Component';
        $ret['rootElementClass'] = 'kwfUp-webStandard kwfUp-webForm';
        $ret['flags']['skipFulltext'] = true;
        $ret['flags']['noIndex'] = true;
        $ret['componentName'] = trlKwfStatic('LostPassword Component');
        return $ret;
    }
}
