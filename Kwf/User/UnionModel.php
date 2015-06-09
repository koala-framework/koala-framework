<?php
class Kwf_User_UnionModel extends Kwf_Model_Union
{
    protected $_columnMapping = 'Kwf_User_UserMapping';
    protected $_rowClass = 'Kwf_User_UnionRow';

    public function getAuthMethods()
    {
        if (!isset($this->_authMethods)) {
            $this->_authMethods = array();

            $methods = array();
            foreach ($this->getUnionModels() as $km=>$m) {
                foreach ($m->getAuthMethods($this) as $ka=>$auth) {
                    $methods[$km.'_'.$ka] = $auth;
                }
            }
            $redirects = array();
            foreach ($methods as $k=>$auth) {
                if (!in_array($auth, $methods)) continue; //happens when $methods is modified during iteration
                if ($auth instanceof Kwf_User_Auth_Interface_Redirect) {
                    $redirects[$k] = array($auth);
                    unset($methods[$k]);
                    foreach ($methods as $k2=>$auth2) {
                        if ($auth2 instanceof Kwf_User_Auth_Interface_Redirect && $auth->isRedirectCompatibleWith($auth2)) {
                            $redirects[$k][] = $auth2;
                            unset($methods[$k2]);
                        }
                    }
                }
            }

            foreach ($redirects as $k=>$auths) {
                if (count($auths) == 1) {
                    $this->_authMethods[$k] = new Kwf_User_Auth_Union_Redirect(
                        $auths[0], $this
                    );
                } else {
                    $this->_authMethods[$k] = new Kwf_User_Auth_Union_Redirects(
                        $auths, $this
                    );
                }
            }
            foreach ($methods as $k=>$auth) {
                if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                    $this->_authMethods[$k] = new Kwf_User_Auth_Union_Password(
                        $auth, $this
                    );
                } else if ($auth instanceof Kwf_User_Auth_Interface_AutoLogin) {
                    $this->_authMethods[$k] = new Kwf_User_Auth_Union_AutoLogin(
                        $auth, $this
                    );
                } else if ($auth instanceof Kwf_User_Auth_Interface_Activation) {
                    $this->_authMethods[$k] = new Kwf_User_Auth_Union_Activation(
                        $auth, $this
                    );
                } else {
                    throw new Kwf_Exception_NotYetImplemented();
                }

            }
        }
        return $this->_authMethods;
    }

    public function logLogin(Kwf_Model_Row_Interface $row)
    {
        $proxyRow = $row->getSourceRow();
        $proxyRow->getModel()->logLogin($proxyRow);
    }


    public function isEqual(Kwf_Model_Interface $other)
    {
        return $this === $other;
    }
}
