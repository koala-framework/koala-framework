<?php
abstract class Kwf_Model_DbWithConnection_SelectExpr_AbstractTest extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SelectExpr_Model1')->setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SelectExpr_Model2')->setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SelectExpr_Model1')->dropTable();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SelectExpr_Model2')->dropTable();
    }

}
