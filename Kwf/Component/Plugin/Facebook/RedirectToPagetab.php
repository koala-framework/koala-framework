<?php
/*
// * this plugin redirects the user to the pagetab, when comming from a friend_request
* e.g. we use this plugin for a fangate through the signed request on the pagetab
*/
class Kwf_Component_Plugin_Facebook_RedirectToPagetab extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewReplace
{
    public function replaceOutput($renderer)
    {
        if (!Kwf_Config::getValue('kwc.fbAppData.pageTabUrl')) {
            throw new Kwf_Exception('kwc.fbAppData.pageTabUrl has to be set in config');
        }
        $facebook = Kwf_Util_Facebook_Api::getInstance();
        $userId = $facebook->getUser();
        $signedRequest = $facebook->getSignedRequest();
        // check, if user is authed, or if we are on the right pagetab
        //(that information is provided by the signedRequest)
        if (isset($_REQUEST['request_ids'])) {
            echo '<script>top.location.href = "'.Kwf_Config::getValue('kwc.fbAppData.pageTabUrl').'";</script>';
            exit;
        } else {
            return false;
        }
    }
}
