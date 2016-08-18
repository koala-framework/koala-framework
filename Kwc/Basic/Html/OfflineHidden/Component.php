<?php
class Kwc_Basic_Html_OfflineHidden_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Offline hidden Html');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        if (!Kwf_Registry::get('config')->isOnline) {
            $ret['content'] = trlKwf('(offline hidden)');
        }
        return $ret;
    }
}
