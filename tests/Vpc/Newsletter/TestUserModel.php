<?php
class Vpc_Newsletter_TestUserModel extends Vps_Model_FnF
{
    protected $_rowClass = 'Vpc_Newsletter_TestUserRow';

    public function __construct($config = array())
    {
        $config = array(
            'columns' => array('id'),
            'primaryKey' => 'id',
            'data'=> array(
                array('id' => 1)
            )
        );
        parent::__construct($config);
    }
}