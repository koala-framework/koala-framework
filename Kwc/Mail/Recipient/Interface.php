<?php
interface Vpc_Mail_Recipient_Interface
{
    const MAIL_FORMAT_TEXT = 'text';
    const MAIL_FORMAT_HTML = 'html';

    public function getMailFirstname();
    public function getMailLastname();
    public function getMailEmail();
    public function getMailFormat();
}
