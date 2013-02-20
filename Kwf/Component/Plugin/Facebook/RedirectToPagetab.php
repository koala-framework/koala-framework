<?php
/*
// * this plugin allways redirects the user to the pagetab
* e.g. we use this plugin for a fangate through the signed request on the pagetab
*/
class Kwf_Component_Plugin_Facebook_RedirectToPagetab extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewReplace
{
    public function replaceOutput()
    {
        if (!Kwf_Config::getValue('kwc.fbAppData.pageTabUrl')) {
            throw new Kwf_Exception('kwc.fbAppData.pageTabUrl has to be set in config');
        }
        $facebook = Kwf_Util_Facebook_Api::getInstance();
        $userId = $facebook->getUser();
        $signedRequest = $facebook->getSignedRequest();
        // check, if user is authed, or if we are on the right pagetab
        //(that information is provided by the signedRequest)
        if (!isset($signedRequest['page']['id'])
            || $signedRequest['page']['id'] != Kwf_Config::getValue('kwc.fbAppData.siteId')) {
            echo '<script>top.location.href = "'.Kwf_Config::getValue('kwc.fbAppData.pageTabUrl').'";</script>"';
            exit;
        } else {
            return false;
        }
    }
}