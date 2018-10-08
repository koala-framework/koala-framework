<?php
class Kwf_Util_RobotsTxt
{
    public static function output()
    {
        $contents = "User-agent: *\n".
            "Disallow: /admin/\n";

        $contents .= "Sitemap: http".(isset($_SERVER['HTTPS']) ? 's' : '')."://"
                        . $_SERVER['HTTP_HOST'] . "/sitemap.xml\n";

        Kwf_Media_Output::output(array(
            'contents' => $contents,
            'mimeType' => 'text/plain'
        ));
    }
}
