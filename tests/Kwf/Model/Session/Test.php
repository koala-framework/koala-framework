<?php
/**
 * @group Model
 * @group Model_Session
 * @group selenium
 * @group slow
 */
class Kwf_Model_Session_Test extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        //RowObserver brauchen wir hier nicht
        Kwf_Component_Data_Root::setComponentClass(false);
    }
    
    public function tearDown()
    {
        parent::tearDown();
        Kwf_Component_Data_Root::setComponentClass(null);
    }

    public function testModel()
    {
        $this->open('/kwf/test/kwf_model_session_test/model-get');
        $this->assertTextPresent("bar");

        $this->open('/kwf/test/kwf_model_session_test/model-set');
        $this->assertTextPresent("OK");

        $this->open('/kwf/test/kwf_model_session_test/model-get');
        $this->assertTextPresent("bum");
    }
}
