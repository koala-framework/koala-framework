<?php
class Kwf_Util_Redirect
{
    public static function redirect($url)
    {
        $url = (string)$url;
        if (!$url) $url = '/';
        if (substr($url, 0, strlen(Kwf_Setup::getBaseUrl())+1) !== Kwf_Setup::getBaseUrl().'/') throw new Kwf_Exception('Invalid Url');
        header('Location: ' . $url);
        exit;
    }
}
