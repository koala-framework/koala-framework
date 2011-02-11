<?php
Zend_Date::setOptions(array(
    'format_type' => 'php'
));
class Vps_Date extends Zend_Date
{
    public function __construct($date = null, $part = null, $locale = null)
    {
        if (!$locale) $locale = Vps_Trl::getInstance()->getTargetLanguage();
        parent::__construct($date, $part, $locale);
    }
}