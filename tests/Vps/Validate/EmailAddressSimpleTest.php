<?php
/**
 * @group Validate
 */
class Vps_Validate_EmailAddressSimpleTest extends Vps_Test_TestCase
{
    private $_v;
    public function setUp()
    {
        parent::setUp();
        $this->_v = new Vps_Validate_EmailAddressSimple();
    }

    public function testValid()
    {
        $this->assertTrue($this->_v->isValid("foo@vivid.com"));

        $this->assertFalse($this->_v->isValid("äähm §/("));
        $this->assertEquals(1, count($this->_v->getMessages()));

        $this->assertFalse($this->_v->isValid("foo@foo"));
        $this->assertEquals(1, count($this->_v->getMessages()));

    }
}
