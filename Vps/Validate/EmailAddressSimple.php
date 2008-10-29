<?php
/**
 * Validierer Einfachere Validierungs-Meldungen
 */
class Vps_Validate_EmailAddressSimple extends Vps_Validate_EmailAddress
{
    protected function _error($messageKey = null, $value = null)
    {
        parent::_error(self::INVALID, $value);
    }

    public function getMessages()
    {
        $messages = parent::getMessages();
        return array(current($messages));
    }
}
