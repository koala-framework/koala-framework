<?php
class Kwf_Util_Redirect
{
    public static function redirect($url)
    {
        $url = (string)$url;
        if (!$url) $url = '/';
        if (substr($url, 0, 1) !== '/') throw new Kwf_Exception('Invalid Url');
        header('Location: ' . $url);
        exit;
    }
}
