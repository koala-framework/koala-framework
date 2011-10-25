<?php
class Kwc_Newsletter_TestUserModel extends Kwf_Model_FnF
{
    protected $_rowClass = 'Kwc_Newsletter_TestUserRow';

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