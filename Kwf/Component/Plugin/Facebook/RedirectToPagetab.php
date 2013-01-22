<?php
class Kwf_Component_Plugin_Facebook_RedirectToPagetab extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewReplace
{
    public function replaceOutput()
    {
        if (!Kwf_Config::getValue('kwc.fbAppData.pageTabUrl')) {
            throw new Kwf_Exception('kwc.fbAppData.pageTabUrl has to be set in config');
        }
        $facebook = Kwf_Util_Facebook_Api::getInstance();
        // check, if user is authed, or if a signed_request is provided
        //(if that is the case, we are allready on the pagetab)
        if (!$facebook->getUser() && !$facebook->getSignedRequest()) {
            return '<script>top.location.href = "'.Kwf_Config::getValue('kwc.fbAppData.pageTabUrl').'";</script>"';
        }
        return false;
    }
}