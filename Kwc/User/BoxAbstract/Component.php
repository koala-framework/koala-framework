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
            }
        }

        if (isset($postData['logout'])) {

            Kwf_Auth::getInstance()->clearIdentity();
            setcookie('feAutologin', '', time() - 3600);

            $url = Kwf_Setup::getRequestPath();

            parse_str($_SERVER['QUERY_STRING'], $queryStr);
            unset($queryStr['logout']);
            $queryStr = http_build_query($queryStr);

            //check if page still exists
            Kwf_Component_Generator_Abstract::clearInstances(); //das ist notwendig da die generator ohne eingeloggten user was anderes zurück geben könnten und das aber im data->getChildComponents gecached ist
            if (!Kwf_Component_Data_Root::getInstance()->getPageByUrl('http://'.$_SERVER['HTTP_HOST'].$url, null)) {
                $url = '/';
                $queryStr = '';
            }

            header('Location: '.$url. ($queryStr ? '?'.$queryStr : ''));
            exit;
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
