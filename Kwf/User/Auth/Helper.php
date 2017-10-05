<?php
class Kwf_User_Auth_Helper
{
    public static function getRedirectFormOptionsHtml($formOptions)
    {
        $ret = '';
        foreach ($formOptions as $formOption) {
            if ($formOption['type'] == 'select') {
                $ret .= $formOption['label'].': <select name="'.$formOption['name']."\">\n";
                foreach ($formOption['values'] as $i) {
                    $ret .= "<option value=\"".Kwf_Util_HtmlSpecialChars::filter($i['value'])."\">".Kwf_Util_HtmlSpecialChars::filter($i['name'])."</option>\n";
                }
                $ret .= "</select>\n";
            }
        }
        return $ret;
    }
}
