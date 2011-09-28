<?php
/**
 * @group Util
 */
class Vps_Util_CheckIpTest extends Vps_Test_TestCase
{
    private $_standardChecker = 'Vps_Util_CheckIpMock';

    /**
     * @expectedException Vps_Util_Check_Ip_Exception
     */
    public function testCheckIpFalseIp()
    {
        Vps_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('192.168.0.3');
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testCheckNoIp()
    {
        Vps_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('intern.vivid-planet.com');
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testCheckWrongMock()
    {
        Vps_Util_Check_Ip::getInstance('Vps_Util_CheckIpMockError')->checkIp('192.168.0.1');
    }

    public function testCheckIpReturn()
    {
        $res = Vps_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('192.168.0.1', true);
        $this->assertTrue($res);
        $res = Vps_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('192.168.0.1', true);
        $this->assertTrue($res);
        $res = Vps_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('192.168.123.9', true);
        $this->assertTrue($res);
        $res = Vps_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('192.168.0.3', true);
        $this->assertFalse($res);
    }
}
