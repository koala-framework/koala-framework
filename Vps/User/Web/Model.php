<?php
final class Vps_User_Web_Model extends Vps_Model_Db
{
    protected $_table = 'vps_users';
    protected $_rowClass = 'Vps_User_Web_Row';
    /*
    protected $_referenceMap = array(
        'User' => array(
            'column' => 'id',
            'refModelClass' => 'Vps_User_Model' // muss hier hardcodet sein, sonst endlos
        )
    );
    */
}
