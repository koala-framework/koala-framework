<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Form_MultiFields
 */
class Kwf_Form_MultiFields_SelTest extends Kwf_Test_SeleniumTestCase
{
    public function test()
    {
        $this->open('/kwf/test/kwf_form_multi-fields_test');
        $this->waitForConnections();
    }

}
