<?php
class Kwf_View_Helper_StatisticCode
{
    public function statisticCode($analyticsCode = null)
    {
        $ret  = '';

        $cfg = Kwf_Config::getValueArray('statistic');
        if (isset($cfg['ignore']) && $cfg['ignore']) {
            return $ret;
        }

        if (Kwf_Registry::get('config')->kwc->domains) {
            throw new Kwf_Exception('do not use helper statisticCode for webs with domains, use Kwc_Statistics_Piwik_Component as box instead.');
        }

        if (!$analyticsCode) {
            $analyticsCode = isset($cfg['analyticsCode']) ? $cfg['analyticsCode'] : false;
        }
        if ($analyticsCode && !$cfg['ignoreAnalyticsCode']) {
            $ret .= "<script type=\"text/javascript\">\n";
            $ret .= "\n";
            $ret .= "    var _gaq = _gaq || [];\n";
            $ret .= "    _gaq.push(['_setAccount', '".$analyticsCode."']);\n";
            $ret .= "    _gaq.push (['_gat._anonymizeIp']);\n";
            $ret .= "    _gaq.push(['_trackPageview']);\n";
            $ret .= "\n";
            $ret .= "    (function() {\n";
            $ret .= "        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n";
            $ret .= "        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n";
            $ret .= "        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n";
            $ret .= "    })();\n";
            $ret .= "\n";
            $ret .= "</script>\n";
        }

        $piwikDomain = isset($cfg['piwikDomain']) ? $cfg['piwikDomain'] : false;
        $piwikId = isset($cfg['piwikId']) ? $cfg['piwikId'] : false;
        if ($piwikDomain && $piwikId && !$cfg['ignorePiwikCode']) {
            $ret .= '<!-- Piwik -->';
            $ret .= '<script type="text/javascript">';
            $ret .= '  var _paq = _paq || [];';
            $ret .= '  _paq.push(["trackPageView"]);';
            $ret .= '  _paq.push(["enableLinkTracking"]);';
            $ret .= '  (function() {';
            $ret .= '    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://'.$piwikDomain.'/";';
            $ret .= '    _paq.push(["setTrackerUrl", u+"piwik.php"]);';
            $ret .= '    _paq.push(["setSiteId", "'.$piwikId.'"]);';
            $ret .= '    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";';
            $ret .= '    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);';
            $ret .= '  })();';
            $ret .= '</script><noscript><p><img src="http://'.$piwikDomain.'/piwik.php?idsite='.$piwikId.'" style="border:0" alt="" /></p></noscript>';
            $ret .= '<!-- End Piwik Code -->';
        }

        return $ret;
    }
}
