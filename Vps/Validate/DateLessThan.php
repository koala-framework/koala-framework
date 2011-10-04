<?php
class Vps_Validate_DateLessThan extends Zend_Validate_Abstract
{
    const NOT_LESS = 'notLessThan';

    protected $_messageVariables = array(
        'max' => '_maxDate'
    );

    protected $_max;
    protected $_maxDate;

    public function __construct($max)
    {
        $this->setMax($max);
        $this->_messageTemplates = array(
            self::NOT_LESS => "'%value%' is not less than '%max%'"
        );
    }

    public function getMax()
    {
        return $this->_max;
    }

    public function setMax($max)
    {
        if (!is_numeric($max)) $max = strtotime($max);
        if (!$max) throw new Vps_Exception("Invalid max date");
        $this->_max = $max;
        $this->_maxDate = date(trlVps('Y-m-d'), $max);
        return $this;
    }

    public function isValid($value)
    {
        $value = strtotime($value);
        if ($value) {
            $this->_setValue(date(trlVps('Y-m-d'), $value));
            if ($this->_max <= $value) {
                $this->_error(self::NOT_LESS);
                return false;
            }
        }
        return true;
    }

}
