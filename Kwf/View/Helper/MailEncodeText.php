<?php
class Kwf_View_Helper_MailEncodeText extends Kwf_View_Helper_Abstract_MailLink
{
    public function mailEncodeText($text)
    {
        return $this->encodeText($text);
    }
}
