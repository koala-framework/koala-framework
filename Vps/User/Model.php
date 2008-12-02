<?php
class Vps_User_Model extends Vps_Model_Db
{

    /*
     * Wird irgendwann durch neues Model ersetzt
     */
    public function __construct($config = array())
    {
        $this->_table = Vps_Registry::get('userModel');
        parent::__construct($config);
    }
}