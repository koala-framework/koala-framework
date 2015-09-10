<?php
//used by Kwf_Component_View_Helper_IncludeCode to dynamically include session token if there is any
class Kwf_Component_Dynamic_SessionToken extends Kwf_Component_Dynamic_Abstract
{
    public function getContent()
    {
        $ret = '';
        if (Kwf_Util_SessionToken::getSessionToken()) {
            $indent = str_repeat(' ', 8);
            $ret  = "<script type=\"text/javascript\">\n";
            $kwf = 'Kwf';
            if ($uniquePrefix = Kwf_Config::getValue('application.uniquePrefix')) {
                $ret .= $indent."if (typeof $uniquePrefix == 'undefined') $uniquePrefix = {};\n";
                $kwf = $uniquePrefix.'.'.$kwf;
            }
            $ret .=$indent."if (typeof $kwf == 'undefined') $kwf = {};\n";
            $ret .= "$kwf.sessionToken = '".Kwf_Util_SessionToken::getSessionToken()."';\n";
            $ret .= "</script>\n";
        }
        return $ret;
    }
}
