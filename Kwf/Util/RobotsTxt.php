<?php
class Kwf_Util_RobotsTxt
{
    public static function output()
    {
        $baseUrl = Kwf_Setup::getBaseUrl();
        $contents = "User-agent: *\n".
            "Disallow: $baseUrl/admin/\n";

        $contents .= "Sitemap: http".(isset($_SERVER['HTTPS']) ? 's' : '')."://"
                        .$_SERVER['HTTP_HOST'].$baseUrl."/sitemap.xml\n";

        Kwf_Media_Output::output(array(
            'contents' => $contents,
            'mimeType' => 'text/plain'
        ));
    }
}
