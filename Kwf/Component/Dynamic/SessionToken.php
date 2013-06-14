<?php
//used by Kwf_Component_View_Helper_IncludeCode to dynamically include session token if there is any
class Kwf_Component_Dynamic_SessionToken extends Kwf_Component_Dynamic_Abstract
{
    public function getContent()
    {
        $ret = '';
        if (Kwf_Util_SessionToken::getSessionToken()) {
            $ret  = "<script type=\"text/javascript\">\n";
            $ret .= "Kwf.sessionToken = '".Kwf_Util_SessionToken::getSessionToken()."';\n";
            $ret .= "</script>\n";
        }
        return $ret;
    }
}
