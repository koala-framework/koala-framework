<?php
class Kwc_User_Login_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_User_Login_Form_Component';
        $ret['cssClass'] = 'webStandard';
        $ret['plugins'] = array('Kwc_User_Login_Plugin');
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

    public function preProcessInput($postData)
    {
        if (isset($postData['redirectAuth'])) {
            $authMethods = Kwf_Registry::get('userModel')->getAuthMethods();
            if (!isset($authMethods[$postData['redirectAuth']])) throw new Kwf_Exception_NotFound();
            $auth = $authMethods[$postData['redirectAuth']];
            if (!$auth instanceof Kwf_User_Auth_Interface_Redirect) throw new Kwf_Exception_NotFound();
            $redirectBackUrl = $_GET['redirect'];

            $formValues = array();
            foreach ($auth->getLoginRedirectFormOptions() as $option) {
                if ($option['type'] == 'select') {
                    $formValues[$option['name']] = $postData[$option['name']];
                }
            }

            $f = new Kwf_Filter_StrongRandom();
            $state = 'login-'.$postData['redirectAuth'].'-'.$f->filter(null).'-'.$redirectBackUrl;

            //save state in namespace to validate it later
            $ns = new Kwf_Session_Namespace('kwf-login-redirect');
            $ns->state = $state;

            $url = $auth->getLoginRedirectUrl($this->_getRedirectBackUrl(), $state, $formValues);
            header("Location: ".$url);
            exit;
        }
        if ($postData != array() && array_keys($postData) != array('redirect')) {
            $user = null;
            foreach (Kwf_Registry::get('userModel')->getAuthMethods() as $auth) {
                if ($auth instanceof Kwf_User_Auth_Interface_Redirect) {
                    $user = $auth->getUserToLoginByParams($postData);
                    if ($user) break;
                }
            }
            if ($user) {
                Kwf_Registry::get('userModel')->loginUserRow($user, false);
                $url = $this->_getUrlForRedirect($postData, $user);
                Kwf_Util_Redirect::redirect($url);
            }
        }
    }

    public final function getUrlForRedirect($postData, $user) {
        return $this->_getUrlForRedirect($postData, $user);
    }

    protected function _getUrlForRedirect($postData, $user)
    {
        if (!empty($postData['redirect']) && substr($postData['redirect'], 0, 1) == '/') {
            $url = $postData['redirect'];
        } else {
            $url = Kwf_Component_Data_Root::getInstance()
            ->getChildPage(array('home' => true, 'subroot' => $this->getData()), array());
        }
        return $url;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['register'] = $this->_getRegisterComponent();
        $ret['lostPassword'] = $this->_getLostPasswordComponent();

        $ret['redirects'] = array();
        foreach (Kwf_Registry::get('userModel')->getAuthMethods() as $authKey=>$auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Redirect && $auth->showInFrontend()) {
                $label = $auth->getLoginRedirectLabel();
                $ret['redirects'][] = array(
                    'url' => $this->getData()->url,
                    'authMethod' => $authKey,
                    'redirect' => '%redirect%',
                    'name' => $this->getData()->trlStaticExecute($label['name']),
                    'icon' => isset($label['icon']) ? '/assets/'.$label['icon'] : false,
                    'formOptions' => Kwf_User_Auth_Helper::getRedirectFormOptionsHtml($auth->getLoginRedirectFormOptions()),
                );
            }
        }

        return $ret;
    }

    protected function _getRegisterComponent()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Kwc_User_Register_Component',
                array('subroot' => $this->getData())
            );
    }

    protected function _getLostPasswordComponent()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Kwc_User_LostPassword_Component',
                array('subroot' => $this->getData())
            );
    }
}
