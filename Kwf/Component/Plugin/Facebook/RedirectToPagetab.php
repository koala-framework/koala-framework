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
        $userId = $facebook->getUser();
        $signedRequest = $facebook->getSignedRequest();
        // check, if user is authed, or if we are on the right pagetab
        //(that information is provided by the signedRequest)
        if (!$userId) {
            if (!isset($signedRequest['page']['id'])
                || $signedRequest['page']['id'] != Kwf_Config::getValue('kwc.fbAppData.siteId')) {
                return '<script>top.location.href = "'.Kwf_Config::getValue('kwc.fbAppData.pageTabUrl').'";</script>"';
            }
        } else {
            // We have a user ID, so probably a logged in user.
            // If not, we'll get an exception, which we handle below.
            try {
                $likes = $facebook->api('/'.$userId.'/likes');
                $fanSiteId = trim(Kwf_Config::getValue('kwc.fbAppData.siteId'));
                foreach ($likes['data'] as $l) {
                    if ($l['id'] == $fanSiteId) {
                        return false;
                    }
                }
                if (!isset($signedRequest['page']['id'])
                    || $signedRequest['page']['id'] != Kwf_Config::getValue('kwc.fbAppData.siteId')) {
                    return '<script>top.location.href = "'.Kwf_Config::getValue('kwc.fbAppData.pageTabUrl').'";</script>"';
                }
            } catch(FacebookApiException $e) {
                // If the user is logged out, you can have a
                // user ID even though the access token is invalid.
                // In this case, we'll get an exception, so we'll
                // send the user back to the pagetab
                return '<script>top.location.href = "'.Kwf_Config::getValue('kwc.fbAppData.pageTabUrl').'";</script>"';
            }
        }
        return false;
    }
}