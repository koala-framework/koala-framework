<?php
interface Kwc_Mail_Recipient_GenderInterface extends Kwc_Mail_Recipient_Interface
{
    const MAIL_GENDER_MALE = 'male';
    const MAIL_GENDER_FEMALE = 'female';

    public function getMailGender();
}
