<?php
class Kwf_User_Auth_Union_Redirects extends Kwf_User_Auth_Abstract implements Kwf_User_Auth_Interface_Redirect
{
    protected $_auths;
    protected $_model;

    public function __construct(array $auths, Kwf_Model_Union $model)
    {
        $this->_auths = $auths;
        $this->_model = $model;
    }

    public function getInnerAuths()
    {
        return $this->_auths;
    }

    public function showInFrontend()
    {
        foreach ($this->_auths as $auth) {
            if ($auth->showInFrontend()) return true;
        }
        return false;
    }

    public function showInBackend()
    {
        foreach ($this->_auths as $auth) {
            if ($auth->showInBackend()) return true;
        }
        return false;
    }

    public function getLoginRedirectLabel()
    {
        foreach ($this->_auths as $auth) {
            return $auth->getLoginRedirectLabel();
        }
    }

    public function getLoginRedirectFormOptions()
    {
        foreach ($this->_auths as $auth) {
            return $auth->getLoginRedirectFormOptions();
        }
    }

    public function getLoginRedirectUrl($redirectBackUrl, $state, $formValues)
    {
        foreach ($this->_auths as $auth) {
            return $auth->getLoginRedirectUrl($redirectBackUrl, $state, $formValues);
        }
    }

    public function getUserToLoginByParams(array $params)
    {
        foreach ($this->_auths as $auth) {
            $row = $auth->getUserToLoginByParams($params);
            if ($row) {
                foreach ($this->_model->getUnionModels() as $k=>$m) {
                    if ($m == $row->getModel()) {
                        $id = $k.$row->{$m->getPrimaryKey()};
                        return $this->_model->getRowById($id);
                    }
                }
                throw new Kwf_Exception("Invalid User returned");
            }
        }
        return null;
    }

    public function getUserToLoginByCallbackParams($redirectBackUrl, array $params)
    {
        foreach ($this->_auths as $auth) {
            $row = $auth->getUserToLoginByCallbackParams($redirectBackUrl, $params);

            if ($row) {
                foreach ($this->_model->getUnionModels() as $k=>$m) {
                    if ($m == $row->getModel()) {
                        $id = $k.$row->{$m->getPrimaryKey()};
                        return $this->_model->getRowById($id);
                    }
                }
                throw new Kwf_Exception("Invalid User returned");
            }
        }
        return null;
    }

    public function associateUserByCallbackParams(Kwf_Model_Row_Interface $user, $redirectBackUrl, array $params)
    {
        foreach ($this->_auths as $auth) {
            if ($user->getSourceRow()->getModel() == $auth->getModel()) {
                $auth->associateUserByCallbackParams($user->getSourceRow(), $redirectBackUrl, $params);
            }
        }
    }

    public function allowPasswordForUser(Kwf_Model_Row_Interface $user)
    {
        foreach ($this->_auths as $auth) {
            if (!$auth->allowPasswordForUser($user->getSourceRow())) return false;
        }
        return true;
    }

    public function isRedirectCompatibleWith(Kwf_User_Auth_Interface_Redirect $auth)
    {
        return false;
    }
}
