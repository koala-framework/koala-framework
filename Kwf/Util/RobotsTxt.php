<?php
class Kwf_Util_RobotsTxt
{
    public static function output()
    {
        $contents = "User-agent: *\n".
            "Disallow: /admin/\n".
            "Disallow: /kwf/util/kwc/render\n"; //used to load eg. lightbox content async, we don't want getting that indexed

        $contents .= "Sitemap: http".(isset($_SERVER['HTTPS']) ? 's' : '')."://"
                        .$_SERVER['HTTP_HOST'].Kwf_Setup::getBaseUrl()."/sitemap.xml\n";

        Kwf_Media_Output::output(array(
            'contents' => $contents,
            'mimeType' => 'text/plain'
        ));
    }
}
