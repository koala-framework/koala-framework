<?php
abstract class Vps_Model_DbWithConnection_SelectExpr_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1')->setUp();
        Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model2')->setUp();
    }

    public function tearDown()
    {
        Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1')->dropTable();
        Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model2')->dropTable();
    }

}
