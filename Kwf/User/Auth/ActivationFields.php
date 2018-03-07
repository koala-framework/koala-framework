<?php
/**
 * Implements an activation token that is saved to activate_token field in user model.
 *
 * Token times out after 24h
 */
class Kwf_User_Auth_ActivationFields extends Kwf_User_Auth_Abstract implements Kwf_User_Auth_Interface_Activation
{
    protected function _getValidityDurationInDays($type)
    {
        return ($type == self::TYPE_ACTIVATE) ? 7 : 1;
    }

    public function validateActivationToken(Kwf_Model_Row_Interface $row, $token)
    {
        if (!$row->activate_token) return false;
        $activateToken = explode(':', $row->activate_token);
        $expire = $activateToken[0];
        $rowToken = $activateToken[1];
        if ($expire < time()) return false;
        if (Kwf_User_Auth_Bcrypt::validateValue($row, $token, $rowToken) === $rowToken) {
            return true;
        }
        return false;
    }

    public function generateActivationToken(Kwf_Model_Row_Interface $row, $type)
    {
        $token = Kwf_Util_Hash::hash(microtime(true).uniqid('', true).mt_rand());
        $days = $this->_getValidityDurationInDays($type);
        $expire = time()+$days*24*60*60;
        $row->activate_token = $expire.':'.Kwf_User_Auth_Bcrypt::encodeValue($row, $token);
        $row->save();
        return $token;
    }

    public function isActivated(Kwf_Model_Row_Interface $row)
    {
        return !$row->activate_token;
    }

    public function clearActivationToken(Kwf_Model_Row_Interface $row)
    {
        $row->activate_token = null;
        $row->save();
        return true;
    }
}
