<?php
class E3_Controller_Action extends Zend_Controller_Action
{
    protected function createDao()
    {
        $dbConfig = new Zend_Config_Ini('../application/config.db.ini', 'database');
        return new E3_Dao($dbConfig);
    }
}
