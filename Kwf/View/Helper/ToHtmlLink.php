<?php
class Kwf_View_Helper_ToHtmlLink
{
    public function toHtmlLink($str)
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
