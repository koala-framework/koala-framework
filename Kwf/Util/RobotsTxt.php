<?php
class Kwf_Util_RobotsTxt
{
    public static function output(Kwf_Component_Data $data)
    {
        $baseUrl = Kwf_Setup::getBaseUrl();
        $contents = "User-agent: *" . PHP_EOL;
        $contents .= "Sitemap: http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://" . $_SERVER['HTTP_HOST'] . $baseUrl . "/sitemap.xml" . PHP_EOL;

        if ($entries = $data->getBaseProperty('robotsTxt')) {
            if (!is_array($entries)) {
                $entries = array($entries);
            }

            foreach ($entries as $entry) {
                $contents .= $entry. PHP_EOL;
            }
        }

        Kwf_Media_Output::output(array(
            'contents' => $contents,
            'mimeType' => 'text/plain'
        ));
    }
}
