<?php
/**
 * @group Model_Session
 * @group selenium
 */
class Vps_Model_Session_Test extends Vps_Test_SeleniumTestCase
{
    public function testException()
    {
        $this->setExpectedException('Vps_Model_Session_TestException');
        $this->open('/vps/test/vps_model_session_test/test-exception');
    }

    public function testModel()
    {
        $model = Vps_Model_Abstract::getInstance('Vps_Model_Session_TestModel');

        $this->open('/vps/test/vps_model_session_test/model-get');
        $this->assertTextPresent("bar");

        $this->open('/vps/test/vps_model_session_test/model-set');
        $this->assertTextPresent("OK");

        $this->open('/vps/test/vps_model_session_test/model-get');
        $this->assertTextPresent("bum");

        $model->reloadSession();
        $row = $model->getRow(1);
        $this->assertEquals('bum', $row->foo);
    }
}
