<?php
final class Kwf_User_Web_Model extends Kwf_Model_Db
{
    protected $_table = 'kwf_users';
    protected $_rowClass = 'Kwf_User_Web_Row';
    protected $_referenceMap = array(
        'User' => array(
            'column' => 'id',
            'refModelClass' => 'Kwf_User_Model' // muss hier hardcodet sein, sonst endlos
        )
    );
}
