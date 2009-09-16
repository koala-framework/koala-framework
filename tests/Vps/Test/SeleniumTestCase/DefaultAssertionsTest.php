<?php
/**
 * @group slow
 */
class Vps_Test_SeleniumTestCase_DefaultAssertionsTest extends Vps_Test_SeleniumTestCase
{
    public function testFatalError()
    {
        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->open('/vps/test/vps_test_selenium-test-case_test/fatal-error');
    }

    public function testNotFound()
    {
        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->open('/blahblub');
    }

    public function testException()
    {
        $this->setExpectedException('Vps_Exception');
        $this->open('/vps/test/vps_test_selenium-test-case_test/exception');
    }
}
