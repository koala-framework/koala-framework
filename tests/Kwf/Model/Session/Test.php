<?php
/**
 * @group Model
 * @group Model_Session
 * @group selenium
 * @group slow
 */
class Vps_Model_Session_Test extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        //RowObserver brauchen wir hier nicht
        Vps_Component_Data_Root::setComponentClass(false);
    }
    
    public function tearDown()
    {
        parent::tearDown();
        Vps_Component_Data_Root::setComponentClass(null);
    }

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
