<?php
/**
 * Interface that says that Vps_Model_Mail may be used with it
 */
interface Vps_Mail_Interface
{
    public function addCc($email, $name = '');
    public function addTo($email, $name = '');
    public function addBcc($email);
    public function addHeader($name, $value, $append = false);
    public function addAttachment(Zend_Mime_Part $attachment);
    public function setFrom($email, $name = null);
    public function setSubject($subject);
    public function setReturnPath($email);
    public function setBodyText($text);
    public function setBodyHtml($html);

    public function getMailContent($type = Vps_Model_Mail_Row::MAIL_CONTENT_AUTO);
    public function getFrom();
    public function getHeaders();
    public function getRecipients();
    public function getReturnPath();
    public function getSubject();
}
