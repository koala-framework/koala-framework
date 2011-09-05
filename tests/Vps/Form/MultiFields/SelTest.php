<?php
/**
 * @group slow
 * @group selenium
 * @group Vps_Form_MultiFields
 */
class Vps_Form_MultiFields_SelTest extends Vps_Test_SeleniumTestCase
{
    public function test()
    {
        $this->open('/vps/test/vps_form_multi-fields_test');
        $this->waitForConnections();
    }

}
