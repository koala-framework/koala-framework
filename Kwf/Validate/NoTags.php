<?php
class Kwf_Validate_NoTags extends Zend_Validate_Abstract
{
    const INVALID_TAGS = 'invalidTags';

    public function __construct()
    {
        $this->_messageTemplates[self::INVALID_TAGS] = trlKwfStatic("Must not include tags");
    }

    public function isValid($value)
    {
        $ret = true;
        if (!is_array($value)) {
            if (strip_tags($value) != $value || stripos($value, 'Content-Type:') !== false) {
                $this->_error(self::INVALID_TAGS);
                $ret = false;
            }
        } else {
            foreach ($value as $val) {
                $ret = $ret && $this->isValid($val);
            }
        }
        return $ret;
    }
}
