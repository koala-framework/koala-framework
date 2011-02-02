<?php
class Vps_Component_Plugin_HideFromBots extends Vps_Component_Plugin_View_Abstract
{
    public function processOutput($output)
    {
        $bots = array(
        'AdsBot-Google',
        'ia_archiver',
        'Scooter/',
        'Ask Jeeves',
        'Baiduspider+(',
        'Exabot/',
        'FAST Enterprise Crawler',
        'FAST-WebCrawler/',
        'http://www.neomo.de/',
        'Gigabot/',
        'Mediapartners-Google',
        'Google Desktop',
        'Feedfetcher-Google',
        'Googlebot',
        'heise-IT-Markt-Crawler',
        'heritrix/1.',
        'ibm.com/cs/crawler',
        'ICCrawler - ICjobs',
        'ichiro/',
        'MJ12bot/',
        'MetagerBot/',
        'msnbot-NewsBlogs/',
        'msnbot/',
        'msnbot-media/',
        'NG-Search/',
        'http://lucene.apache.org/nutch/',
        'NutchCVS/',
        'OmniExplorer_Bot/',
        'psbot/0',
        'Seekbot/',
        'Sensis Web Crawler',
        'SEO search Crawler/',
        'Seoma [SEO Crawler]',
        'SEOsearch/',
        'Snappy/1.1 ( http://www.urltrends.com/ )',
        'http://www.tkl.iis.u-tokyo.ac.jp/~crawler/',
        'SynooBot/',
        'crawleradmin.t-info@telekom.de',
        'TurnitinBot/',
        'voyager/1.0',
        'W3 SiteSearch Crawler',
        'http://www.WISEnutbot.com',
        'yacybot',
        'Yahoo-MMCrawler/',
        'Yahoo! DE Slurp',
        'Yahoo! Slurp',
        'YahooSeeker/'
        );
        $isBot = false;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            foreach ($bots as $bot) {
                if (strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
                    $isBot = true;
                    break;
                }
            }
        }
        if (!$isBot) {
            return $output;
        }
        return '';
    }
}
