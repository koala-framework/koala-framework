<?php
/**
 * Validierer Einfachere Validierungs-Meldungen
 *
 * @package Validate
 */
class Kwf_Validate_EmailAddressSimple extends Kwf_Validate_EmailAddress
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
