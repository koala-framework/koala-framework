<?php
class Kwf_User_Auth_Union_Redirect extends Kwf_User_Auth_Union_Abstract implements Kwf_User_Auth_Interface_Redirect
{
    public function getLoginRedirectLabel()
    {
        return $this->_auth->getLoginRedirectLabel();
    }

    public function getLoginRedirectUrl($redirectBackUrl)
    {
        return $this->_auth->getLoginRedirectUrl($redirectBackUrl);
    }

    public function getUserToLoginByParams(array $params)
    {
        $row = $this->_auth->getUserToLoginByParams($params);
        if (!$row) return null;

        foreach ($this->_model->getUnionModels() as $k=>$m) {
            if ($m == $row->getModel()) {
                $id = $k.$row->{$m->getPrimaryKey()};
                return $this->_model->getRowById($id);
            }
        }
        return null;
    }

    public function createSampleLoginLinks($absoluteUrl)
    {
        return $this->_auth->createSampleLoginLinks($absoluteUrl);
    }
}
