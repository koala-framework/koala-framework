<?php
class Kwf_User_Auth_Union_Redirect extends Kwf_User_Auth_Union_Abstract implements Kwf_User_Auth_Interface_Redirect
{
    public function showInFrontend()
    {
        return $this->_auth->showInFrontend();
    }

    public function showInBackend()
    {
        return $this->_auth->showInBackend();
    }

    public function getLoginRedirectLabel()
    {
        return $this->_auth->getLoginRedirectLabel();
    }

    public function getLoginRedirectFormOptions()
    {
        return $this->_auth->getLoginRedirectFormOptions();
    }

    public function getLoginRedirectUrl($redirectBackUrl, $state, $formValues)
    {
        return $this->_auth->getLoginRedirectUrl($redirectBackUrl, $state, $formValues);
    }

    public function getUserToLoginByParams($redirectBackUrl, array $params)
    {
        $row = $this->_auth->getUserToLoginByParams($redirectBackUrl, $params);
        if (!$row) return null;

        foreach ($this->_model->getUnionModels() as $k=>$m) {
            if ($m == $row->getModel()) {
                $id = $k.$row->{$m->getPrimaryKey()};
                return $this->_model->getRowById($id);
            }
        }
        return null;
    }

    public function associateUserByParams(Kwf_Model_Row_Interface $user, $redirectBackUrl, array $params)
    {
        $this->_auth->associateUserByParams($user->getSourceRow(), $redirectBackUrl, $params);
    }

    public function createSampleLoginLinks($absoluteUrl)
    {
        return $this->_auth->createSampleLoginLinks($absoluteUrl);
    }

    public function allowPasswordForUser(Kwf_Model_Row_Interface $user)
    {
        return $this->_auth->allowPasswordForUser($user->getSourceRow());
    }
}
