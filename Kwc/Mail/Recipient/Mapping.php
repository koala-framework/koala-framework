<?php
class Kwc_Mail_Recipient_Mapping extends Kwf_Model_ColumnMapping
{
    const MAIL_FORMAT_TEXT = 'text';
    const MAIL_FORMAT_HTML = 'html';

    const MAIL_GENDER_MALE = 'male';
    const MAIL_GENDER_FEMALE = 'female';

    public static $columns = array(
        'firstname',
        'lastname',
        'email',
        'format',
        'title',
        'gender'
    );
}
