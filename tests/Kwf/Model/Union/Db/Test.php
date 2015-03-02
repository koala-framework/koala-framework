<?php
/**
 * @group slow
 */
class Kwf_Model_Union_Db_Test extends Kwf_Model_Union_Abstract_Test
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Db_Model1')->setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Db_Model2')->setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Db_ModelSibling')->setUp();
        $this->_m = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Db_TestModel');
    }

    public function tearDown()
    {
        parent::tearDown();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Db_Model1')->dropTable();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Db_Model2')->dropTable();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_Db_ModelSibling')->dropTable();
    }
}
