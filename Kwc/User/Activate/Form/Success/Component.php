<?php
class Kwc_User_Activate_Form_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['config'] = array(
            'redirectUrl' => '/'
        );
        return $ret;
    }


}
