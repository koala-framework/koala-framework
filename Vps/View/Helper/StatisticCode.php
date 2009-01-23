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
            $ret .= "    var pageTracker = _gat._getTracker(\"".$analyticsCode."\");\n";
            $ret .= "    pageTracker._initData();\n";
            $ret .= "    pageTracker._trackPageview();\n";
            $ret .= "</script>\n";
        }
        return $ret;
    }
}
