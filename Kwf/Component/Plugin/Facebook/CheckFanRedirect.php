<?php
class Kwf_Component_Plugin_Facebook_CheckFanRedirect extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewReplace
{
    public function replaceOutput($renderer)
    {
        if (!Kwf_Config::getValue('kwc.fbAppData.siteId')) {
            throw new Kwf_Exception('kwc.fbAppData.siteId has to be set in config');
        }
        $facebook = Kwf_Util_Facebook_Api::getInstance();
        $userId = $facebook->getUser();
        if (!$userId) {
            $signedRequest = $facebook->getSignedRequest();
            if (!$signedRequest['page']['liked']) {
                $like = false;
            } else {
                $like = true;
            }
        } else {
            $like = false;
            $likes = $facebook->api('/'.$userId.'/likes');
            $fanSiteId = trim(Kwf_Config::getValue('kwc.fbAppData.siteId'));
            foreach ($likes['data'] as $l) {
                if ($l['id'] == $fanSiteId) {
                    $like = true;
                }
            }
        }
        if (!$like) {
            return '<script>top.location.href = "'.$this->_getRedirectPage().'";</script>"';
        }
        return false;
    }
    protected function _getRedirectPage()
    {
        if (!Kwf_Config::getValue('kwc.fbAppData.pageTabUrl')) {
            throw new Kwf_Exception('kwc.fbApp.Data.pageTabUrl has to be set in config');
        }
        return Kwf_Config::getValue('kwc.fbAppData.pageTabUrl');
    }
}
