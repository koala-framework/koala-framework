<?php
class Vps_Date extends Zend_Date
{
    public function __construct($date = null, $part = null, $locale = null)
    {
        if (!$locale) $locale = Zend_Registry::get('trl')->getTargetLanguage();
        parent::__construct($date, $part, $locale);
    }
}