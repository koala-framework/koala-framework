<?php
class Vps_View_Helper_StatisticCode
{
    public function statisticCode($analyticsCode = null)
    {
        $ret  = '';
        $cfg = Vps_Registry::get('config');
        if (!$analyticsCode) {
            $analyticsCode = $cfg->statistic->analyticsCode;
        }
        if ($analyticsCode && !$cfg->statistic->ignoreAnalyticsCode) {
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
        $piwikDomain = $cfg->statistic->piwikDomain;
        $piwikId = $cfg->statistic->piwikId;
        if ($piwikDomain && $piwikId && !$cfg->statistic->ignorePiwikCode) {
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
