<?php
class Kwc_User_Activate_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_User_Activate_Form_Component';
        $ret['cssClass'] = 'webStandard';
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    private function _getRedirectBackUrl()
    {
        $redirectBackUrl = Kwf_Controller_Front::getInstance()->getRouter()->assemble(array(
            'controller' => 'login',
            'action' => 'redirect-callback',
        ), 'kwf_user');
        $redirectBackUrl = 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'
            .$_SERVER['HTTP_HOST']
            .$redirectBackUrl;
        return $redirectBackUrl;
    }

    public function processInput($postData)
    {
        if (isset($postData['redirectAuth'])) {
            $authMethods = Kwf_Registry::get('userModel')->getAuthMethods();
            if (!isset($authMethods[$postData['redirectAuth']])) throw new Kwf_Exception_NotFound();
            $auth = $authMethods[$postData['redirectAuth']];
            if (!$auth instanceof Kwf_User_Auth_Interface_Redirect) throw new Kwf_Exception_NotFound();

            $formValues = array();
            foreach ($auth->getLoginRedirectFormOptions() as $option) {
                if ($option['type'] == 'select') {
                    $formValues[$option['name']] = $postData[$option['name']];
                }
            }

            $redirectBackUrl = Kwf_Setup::getBaseUrl().'/';
            $f = new Kwf_Filter_StrongRandom();
            $state = 'activate-'.$postData['redirectAuth'].'-'.$f->filter(null).'-'.$postData['code'].'-'.$redirectBackUrl;

            //save state in namespace to validate it later
            $ns = new Kwf_Session_Namespace('kwf-login-redirect');
            $ns->state = $state;

            $url = $auth->getLoginRedirectUrl($this->_getRedirectBackUrl(), $state, $formValues);
            header("Location: ".$url);
            exit;
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $users = Kwf_Registry::get('userModel');

        $showPassword = false;

        //is there a password auth?
        foreach ($users->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                $showPassword = true;
            }
        }

        if ($showPassword) {
            //if a redirect auth doesn't allow password hide it
            foreach ($users->getAuthMethods() as $auth) {
                if ($auth instanceof Kwf_User_Auth_Interface_Redirect) {
                    $user = $this->getData()->getChildComponent('-form')->getComponent()->getUserRow();
                    if (!$auth->allowPasswordForUser($user)) {
                        $showPassword = false;
                    }
                }
            }
        }
        $ret['showPassword'] = $showPassword;

        $ret['redirects'] = array();
        foreach ($users->getAuthMethods() as $authKey=>$auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Redirect && $auth->showInFrontend()) {
                $label = $auth->getLoginRedirectLabel();
                $ret['redirects'][] = array(
                    'url' => $this->getData()->url,
                    'code' => $_GET['code'],
                    'authMethod' => $authKey,
                    'name' => $this->getData()->trlStaticExecute($label['name']),
                    'icon' => isset($label['icon']) ? '/assets/'.$label['icon'] : false,
                    'formOptions' => Kwf_User_Auth_Helper::getRedirectFormOptionsHtml($auth->getLoginRedirectFormOptions()),
                );
            }
        }

        return $ret;
    }

}
