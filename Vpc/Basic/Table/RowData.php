<?php
class Vpc_Basic_Table_RowData extends Vps_Model_Proxy_Row
{
    public function getReplacedContent($field)
    {
        return self::replaceContent($this->$field);
    }

    public static function replaceContent($str)
    {
        $mailValidator = new Vps_Validate_EmailAddress();
        $emailHelper = new Vps_View_Helper_MailLink();

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
