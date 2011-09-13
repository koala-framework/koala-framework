<?php
class Vps_View_Helper_StatisticCode
{
    public function statisticCode($analyticsCode = null)
    {
        $ret  = '';
        $cfg = Vps_Config_Web::getValueArray('statistic');
        if (!$analyticsCode) {
            $analyticsCode = isset($cfg['analyticsCode']) ? $cfg['analyticsCode'] : false;
        }
        if ($analyticsCode && !$cfg['ignoreAnalyticsCode']) {
            $ret .= "<script type=\"text/javascript\">\n";
            $ret .= "    var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\n";
            $ret .= "    document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\n";
            $ret .= "</script>\n";
            $ret .= "<script type=\"text/javascript\">\n";
            $ret .= "    try {\n";
            $ret .= "        var pageTracker = _gat._getTracker(\"".$analyticsCode."\");\n";
            $ret .= "        pageTracker._trackPageview();\n";
            $ret .= "    } catch(err) {}\n";
            $ret .= "</script>\n";
        }
        $piwikDomain = isset($cfg['piwikDomain']) ? $cfg['piwikDomain'] : false;
        $piwikId = isset($cfg['piwikId']) ? $cfg['piwikId'] : false;
        if ($piwikDomain && $piwikId && !$cfg['ignorePiwikCode']) {
            $ret .= "<!-- Piwik -->";
            $ret .= "<script type=\"text/javascript\">";
            $ret .= "var pkBaseURL = ((\"https:\" == document.location.protocol) ? \"https://$piwikDomain/\" : \"http://$piwikDomain/\");";
            $ret .= "document.write(unescape(\"%3Cscript src='\" + pkBaseURL + \"piwik.js' type='text/javascript'%3E%3C/script%3E\"));";
            $ret .= "</script><script type=\"text/javascript\">";
            $ret .= "try {";
            $ret .= "var piwikTracker = Piwik.getTracker(pkBaseURL + \"piwik.php\", $piwikId);";
            $ret .= "piwikTracker.trackPageView();";
            $ret .= "piwikTracker.enableLinkTracking();";
            $ret .= "} catch( err ) {}";
            $ret .= "</script><noscript><p><img src=\"http://$piwikDomain/piwik.php?idsite=$piwikId\" style=\"border:0\" alt=\"\" /></p></noscript>";
            $ret .= "<!-- End Piwik Tracking Code -->";
        }
        return $ret;
    }
}
