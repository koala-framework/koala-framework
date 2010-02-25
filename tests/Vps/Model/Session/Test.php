<?php
/**
 * @group Model
 * @group Model_Session
 * @group selenium
 * @group slow
 */
class Vps_Model_Session_Test extends Vps_Test_SeleniumTestCase
{
    public function testModel()
    {
        $this->open('/vps/test/vps_model_session_test/model-get');
        $this->assertTextPresent("bar");

        $this->open('/vps/test/vps_model_session_test/model-set');
        $this->assertTextPresent("OK");

        $this->open('/vps/test/vps_model_session_test/model-get');
        $this->assertTextPresent("bum");
    }
}
