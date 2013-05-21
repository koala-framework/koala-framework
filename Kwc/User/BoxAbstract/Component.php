<?php
class Kwc_User_BoxAbstract_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function preProcessInput($postData)
    {
        if (isset($_COOKIE['feAutologin'])
            && !Kwf_Registry::get('userModel')->getKwfModel()->getAuthedUser()
        ) {
            $feAutologin = explode('.', $_COOKIE['feAutologin']);
            if (count($feAutologin) ==2 ) {
                $result = $this->_getAuthenticateResult($feAutologin[0], $feAutologin[1]);
                if ($result->isValid()) {
                    $_COOKIE[session_name()] = true;
                }
            }
        }
    }

    private function _getAuthenticateResult($identity, $credential)
    {
        $adapter = new Kwf_Auth_Adapter_Service();
        $adapter->setIdentity($identity);
        $adapter->setCredential($credential);

        $auth = Kwf_Auth::getInstance();
        $auth->clearIdentity();
        return $auth->authenticate($adapter);
    }
}
