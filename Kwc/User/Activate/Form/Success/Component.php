<?php
class Kwc_User_Activate_Form_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $redirectUrl = '/';
        if (isset($_REQUEST['redirect']) && $_REQUEST['redirect']
            && substr($_REQUEST['redirect'], 0, 1) === '/'
        ) {
            $redirectUrl = $_REQUEST['redirect'];
        }
        $ret['config'] = array(
            'redirectUrl' => $redirectUrl
        );
        return $ret;
    }
}
