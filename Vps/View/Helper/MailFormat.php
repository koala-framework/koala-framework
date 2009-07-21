<?php
class Vps_View_Helper_MailFormat
{
    public function mailFormat($text, $values)
    {
        foreach ($values as $search => $replace) {
            $text = str_replace($search, $replace, $text);
        }
        return $text;
    }
}
