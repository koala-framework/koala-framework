<?php
class Kwf_Util_RobotsTxt
{
    public static function output(Kwf_Component_Data $data)
    {
        $contents = '';
        $contents .= self::_getUserAgent();
        $contents .= self::_getSitemap();
        $contents .= self::_getCustomEntries($data);
        self::_outputRobots($contents);
    }

    protected static function _getUserAgent()
    {
        return "User-agent: *" . PHP_EOL;
    }

    protected static function _getSitemap()
    {
        return "Sitemap: http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://"
            . $_SERVER['HTTP_HOST'] . "/sitemap.xml"  . PHP_EOL;
    }

    protected static function _getCustomEntries($data)
    {
        $customEntries = '';
        if ($entries = $data->getBaseProperty('robotsTxt')) {
            if (!is_array($entries)) {
                $entries = array($entries);
            }

            foreach ($entries as $entry) {
                $customEntries .= $entry . PHP_EOL;
            }
        }
        return $customEntries;
    }

    protected static function _outputRobots($contents)
    {
        Kwf_Media_Output::output(array(
            'contents' => $contents,
            'mimeType' => 'text/plain'
        ));
    }
}
