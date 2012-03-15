<?php
class Kwf_Validate_Date extends Zend_Validate_Date
{
    public function __construct($options = array())
    {
        if (isset($options['outputFormat'])) {
            $format = $options['outputFormat'];
            unset($options['outputFormat']);
        } else {
            $format = '%format%';
        }
        parent::__construct($options);
        $this->_messageTemplates[self::INVALID] = "Invalid type given, value should be string, integer, array or Zend_Date";
        $this->_messageTemplates[self::INVALID_DATE] = trlKwf("'%value%' does not appear to be a valid date");
        $this->_messageTemplates[self::FALSEFORMAT] = trlKwf("'%value%' does not fit the date format '{0}'", array($format));
    }
}
