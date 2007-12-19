<?php

class Vps_Validate_Time extends Zend_Validate_Abstract
{
    /**
     * Validation failure message key for when the value does not follow the YYYY-MM-DD format
     */
    const NOT_HH_MM = 'timeNOT_HH_MM';

    /**
     * Validation failure message key for when the value does not appear to be a valid date
     */
    const INVALID        = 'timeInvalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_HH_MM => "'%value%' is not of the format HH:MM",
        self::INVALID        => "'%value%' does not appear to be a valid time"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid date of the format YYYY-MM-DD
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;

        $this->_setValue($valueString);

        if (!preg_match('/^\d{2}:\d{2}$/', $valueString)) {
            $this->_error(self::NOT_HH_MM);
            return false;
        }

        list($hour, $minute) = sscanf($valueString, '%d:%d');
        
        if ($hour > 23 || $minute > 59) {
            $this->_error(self::INVALID);
            return false;
        }

        return true;
    }

}