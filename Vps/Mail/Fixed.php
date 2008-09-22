<?php

/*
 * Patch für Bug in Vps
 * Bei Mails wird das Subject falsch gekürzt
 * source: http://framework.zend.com/issues/secure/attachment/11475/r10901.patch
 */
class Vps_Mail_Fixed extends Zend_Mail
{
    //override
    protected function _encodeHeader($value)
    {
        if (Zend_Mime::isPrintable($value)) {
            return $value;
        } else {
            return Vps_Mime::encodeQuotedPrintableHeader($value, 'utf-8');
        }
    }
}
