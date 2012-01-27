<?php
class Kwc_Mail_Recipient_GenderMapping extends Kwf_Model_ColumnMapping
{
    const MAIL_GENDER_MALE = 'male';
    const MAIL_GENDER_FEMALE = 'female';
    public static $columns = array(
        'gender',
    );
}
