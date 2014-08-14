<?php
class Kwf_User_UserMapping extends Kwc_Mail_Recipient_Mapping
{
    public static $columns = array(
        'firstname',
        'lastname',
        'email',
        'format',
        'title',
        'gender',
        'role',
    );
}
