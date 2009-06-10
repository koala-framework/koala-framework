<?php
interface Vpc_Mail_Recipient_Interface
{
    const MAIL_FORMAT_TEXT = 'text';
    const MAIL_FORMAT_HTML = 'html';

    const MAIL_GENDER_MALE = 'male';
    const MAIL_GENDER_FEMALE = 'female';

    public function getMailGender();
    public function getMailTitle();
    public function getMailFirstname();
    public function getMailLastname();
    public function getMailEmail();
    public function getMailFormat();
}
