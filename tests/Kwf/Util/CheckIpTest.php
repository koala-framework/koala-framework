<?php
/**
 * @group Util
 */
class Kwf_Util_CheckIpTest extends Kwf_Test_TestCase
{
    private $_standardChecker = 'Kwf_Util_CheckIpMock';

    /**
     * @expectedException Kwf_Util_Check_Ip_Exception
     */
    public function testCheckIpFalseIp()
    {
        Kwf_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('192.168.0.3');
    }

    /**
     * @expectedException Kwf_Exception
     */
    public function testCheckNoIp()
    {
        Kwf_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('intern.vivid-planet.com');
    }

    /**
     * @expectedException Kwf_Exception
     */
    public function testCheckWrongMock()
    {
        Kwf_Util_Check_Ip::getInstance('Kwf_Util_CheckIpMockError')->checkIp('192.168.0.1');
    }

    public function testCheckIpReturn()
    {
        $res = Kwf_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('192.168.0.1', true);
        $this->assertTrue($res);
        $res = Kwf_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('192.168.0.1', true);
        $this->assertTrue($res);
        $res = Kwf_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('192.168.123.9', true);
        $this->assertTrue($res);
        $res = Kwf_Util_Check_Ip::getInstance($this->_standardChecker)->checkIp('192.168.0.3', true);
        $this->assertFalse($res);
    }
}
