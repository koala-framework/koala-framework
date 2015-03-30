<?php
/**
 * @group Util
 */
class Kwf_Util_MemoryLimitTest extends Kwf_Test_TestCase
{
    private $_currentLimit;

    public function setUp()
    {
        parent::setUp();
        $this->_currentLimit = ini_get('memory_limit');
    }

    public function tearDown()
    {
        parent::tearDown();
        ini_set('memory_limit', $this->_currentLimit);
    }

    public function testSetInMegabytes()
    {
        ini_set('memory_limit', '100M');

        $ret = Kwf_Util_MemoryLimit::set(99);
        $this->assertFalse($ret);
        $this->assertEquals(Kwf_Util_MemoryLimit::get(), 100);

        $ret = Kwf_Util_MemoryLimit::set(101);
        $this->assertTrue($ret);
        $this->assertEquals(Kwf_Util_MemoryLimit::get(), 101);
    }

    /**
     * @expectedException Kwf_Exception
     */
    public function testSetNegative()
    {
        Kwf_Util_MemoryLimit::set(-1);
    }

    public function testSetInMegabytesWithMaxLimit()
    {
        ini_set('memory_limit', '100M');

        Kwf_Util_MemoryLimit::setMaxLimit(101);
        $ret = Kwf_Util_MemoryLimit::set(101);
        $this->assertTrue($ret);
        $this->assertEquals(Kwf_Util_MemoryLimit::get(), 101);
        $ret = Kwf_Util_MemoryLimit::set(102);
        $this->assertFalse($ret);
        $this->assertEquals(Kwf_Util_MemoryLimit::get(), 101);
    }

    public function testConvertToMegabyte()
    {
        $this->assertEquals(100, Kwf_Util_MemoryLimit::convertToMegabyte(100));
        $this->assertEquals(100, Kwf_Util_MemoryLimit::convertToMegabyte('100M'));
        $this->assertEquals(100, Kwf_Util_MemoryLimit::convertToMegabyte('102400K'));
        $this->assertEquals(2048, Kwf_Util_MemoryLimit::convertToMegabyte('2G'));
    }
}
