<?php
class Vps_User_SiblingModel extends Vps_Model_FnF
{
    protected $_primaryKey = 'id';
    protected $_columns = array('id', 'role');
    protected $_referenceMap = array(
        'User' => array(
            'column' => 'id',
            'refModelClass' => 'Vps_User_Model'
        )
    );
}
