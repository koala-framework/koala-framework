<?php
class Kwf_View_Helper_MailLink extends Kwf_View_Helper_Abstract_MailLink
{
    public function mailLink($mailAddress, $linkText = null, $cssClass = null)
    {
        if (!$mailAddress) return $linkText;
        
        $encodedMailAddress = $this->encodeMail($mailAddress);

        if (is_null($linkText)) {
            $linkText = $mailAddress;
        }
        $encodedLinkText = $this->encodeText($linkText);

        $attr = $subjectBody = '';
        if (is_string($cssClass)) {
            $attr = ' class="'.Kwf_Util_HtmlSpecialChars::filter($cssClass).'"';
        } else if (is_array($cssClass)) {
            foreach ($cssClass as $k=>$i) {
                if ($k == 'subject') {
                    $subjectBody .= "?subject=$i";
                } else if ($k == 'body') {
                    if (empty($subjectBody)) {
                        $subjectBody .= '?';
                    } else {
                        $subjectBody .= '&amp;';
                    }
                    $subjectBody .= "body=$i";
                } else {
                    $attr .= ' '.Kwf_Util_HtmlSpecialChars::filter($k).'="'.Kwf_Util_HtmlSpecialChars::filter($i).'"';
                }
            }
        }

        return '<a href="mailto:'.$encodedMailAddress.'"'.$attr.'>'.$encodedLinkText.'</a>';
    }
}
