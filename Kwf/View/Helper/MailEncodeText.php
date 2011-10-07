<?php
class Vps_View_Helper_MailEncodeText extends Vps_View_Helper_Abstract_MailLink
{
    public function mailEncodeText($text)
    {
        return $this->encodeText($text);
    }
}
