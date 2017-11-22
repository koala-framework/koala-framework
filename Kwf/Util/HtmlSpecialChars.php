<?php
class Kwf_Util_HtmlSpecialChars
{
    public static function filter($value)
    {
        return htmlspecialchars($value, ENT_QUOTES);
    }

    public static function decode($value)
    {
        return htmlspecialchars_decode($value, ENT_QUOTES);
    }
}
