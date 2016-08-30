<?php
class Kwc_User_Box_Component extends Kwc_User_BoxWithoutLogin_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['showLoginLink'] = false;
        $ret['generators']['child']['component']['login'] = 'Kwc_User_Box_Login_Component';
        $ret['placeholder']['loginHeadline'] = trlKwfStatic('Login:');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        return $ret;
    }
}
