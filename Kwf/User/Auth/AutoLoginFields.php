<?php
/**
 * Implements an auto login token that is saved to autologin field in user model.
 *
 * Token times out after 100 days
 */
class Kwf_User_Auth_AutoLoginFields extends Kwf_User_Auth_Abstract implements Kwf_User_Auth_Interface_AutoLogin
{
    protected function _getModelSelect()
    {
        return new Kwf_Model_Select();
    }

    public function getRowById($id)
    {
        $s = $this->_getModelSelect();
        $s->whereEquals('id', $id);
        return $this->_model->getRow($s);
    }

    public function clearAutoLoginToken(Kwf_Model_Row_Interface $row)
    {
        $row->autologin = null;
        $row->save();
        return true;
    }

    public function generateAutoLoginToken(Kwf_Model_Row_Interface $row)
    {
        $token = Kwf_Util_Hash::hash(microtime(true).uniqid('', true).mt_rand());
        $expire = time()+100*24*60*60;
        $row->autologin = $expire.':'.Kwf_User_Auth_Bcrypt::encodeValue($row, $token);
        $row->save();
        return $token;
    }

    public function validateAutoLoginToken(Kwf_Model_Row_Interface $row, $token)
    {
        if (!$row->autologin) return false;
        $autologin = explode(':', $row->autologin);
        $expire = $autologin[0];
        $rowToken = $autologin[1];
        if ($expire < time()) return false;
        if (Kwf_User_Auth_Bcrypt::validateValue($row, $token, $rowToken) === $rowToken) {
            return true;
        }
        return false;
    }
}
