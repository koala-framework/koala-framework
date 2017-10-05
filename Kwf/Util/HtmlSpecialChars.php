<?php
class Kwf_Util_HtmlSpecialChars
{
    public static function filter($value)
    {
        return htmlspecialchars($value, ENT_QUOTES);
    }
}
