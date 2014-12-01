<?php
class Kwf_User_UnionModel extends Kwf_Model_Union
{
    protected $_columnMapping = 'Kwf_User_UserMapping';
    protected $_rowClass = 'Kwf_User_UnionRow';

    public function getAuthMethods()
    {
        if (!isset($this->_authMethods)) {
            $this->_authMethods = array();
            foreach ($this->getUnionModels() as $km=>$m) {
                foreach ($m->getAuthMethods($this) as $ka=>$auth) {
                    if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                        $this->_authMethods[$km.'_'.$ka] = new Kwf_User_Auth_Union_Password(
                            $auth, $this
                        );
                    } else if ($auth instanceof Kwf_User_Auth_Interface_AutoLogin) {
                        $this->_authMethods[$km.'_'.$ka] = new Kwf_User_Auth_Union_AutoLogin(
                            $auth, $this
                        );
                    } else {
                        throw new Kwf_Exception_NotYetImplemented();
                    }
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
