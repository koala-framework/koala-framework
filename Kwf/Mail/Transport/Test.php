<?php
/**
 * Use this in unittests, access last mail by public properties
 */
class Kwf_Mail_Transport_Test extends Zend_Mail_Transport_Abstract
{
    protected function _sendMail()
    {
    }
}
