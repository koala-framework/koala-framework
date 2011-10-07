<?php
class Kwc_Basic_Table_RowData extends Kwf_Model_Proxy_Row
{
    public function getReplacedContent($field)
    {
        return self::replaceContent($this->$field);
    }

    public static function replaceContent($str)
    {
        $mailValidator = new Kwf_Validate_EmailAddress();
        $emailHelper = new Kwf_View_Helper_MailLink();

        if (substr($str, 0, 7) == 'http://' || substr($str, 0, 4) == 'www.') {
            if (substr($str, 0, 7) != 'http://') {
                $str = 'http://'.$str;
            }
            return '<a href="'.$str.'">'.substr($str, 7).'</a>';
        }

        if ($mailValidator->isValid($str)) {
            return $emailHelper->mailLink($str);
        }

        return $str;
    }
}
