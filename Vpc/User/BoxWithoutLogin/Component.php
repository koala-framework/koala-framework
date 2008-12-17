<?php
class Vpc_User_BoxWithoutLogin_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['showLostPassword'] = true;
        $ret['linkPostfix'] = '';
        $ret['flags']['processInput'] = true;
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['authedUser'] = Vps_Registry::get('userModel')->getAuthedUser();
        $ret['register'] = Vps_Component_Data_Root::getInstance()
                        ->getComponentByClass(
                            'Vpc_User_Register_Component',
                            array('subroot' => $this->getData())
                        );
        if ($this->_getSetting('showLostPassword')) {
            $ret['lostPassword'] = Vps_Component_Data_Root::getInstance()
                            ->getComponentByClass(
                                'Vpc_User_LostPassword_Component',
                                array('subroot' => $this->getData())
                            );
        }
        $ret['login'] = Vps_Component_Data_Root::getInstance()
                        ->getComponentByClass(
                            'Vpc_User_Login_Component',
                            array('subroot' => $this->getData())
                        );
        if ($ret['authedUser']) {
            $ret['myProfile'] = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass(
                    'Vpc_User_Directory_Component',
                    array('subroot' => $this->getData())
                )
                ->getChildComponent('_' . $ret['authedUser']->id);
            $ret['links'] = $this->_getLinks();
        }

        $ret['linkPostfix'] = $this->_getSetting('linkPostfix');
        return $ret;
    }

    protected function _getLinks()
    {
        $ret = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_User_Edit_Component');
        return $ret;
    }

    public function preProcessInput($postData)
    {
        if (isset($postData['feAutologin'])
            && !Vps_Registry::get('userModel')->getAuthedUser()
        ) {
            list($cookieId, $cookieMd5) = explode('.', $postData['feAutologin']);
            if (!empty($cookieId) && !empty($cookieMd5)) {
                $result = $this->_getAuthenticateResult($cookieId, $cookieMd5);
            }
        }

        if (isset($postData['logout'])) {
            Vps_Auth::getInstance()->clearIdentity();
            setcookie('feAutologin', '', time() - 3600);
        }
    }


    private function _getAuthenticateResult($identity, $credential)
    {
        $adapter = new Vps_Auth_Adapter_Service();
        $adapter->setIdentity($identity);
        $adapter->setCredential($credential);

        $auth = Vps_Auth::getInstance();
        $auth->clearIdentity();
        $result = $auth->authenticate($adapter);

        if ($result->isValid()) {
            $auth->getStorage()->write(array(
                'userId' => $adapter->getUserId()
            ));
        }

        return $result;
    }
}
