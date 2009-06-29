<?php
/**
 * @group slow
 * @group Vps_Form_MultiCheckbox
 */
class Vps_Form_MultiCheckbox_Test extends Vps_Test_SeleniumTestCase
{
    public function test()
    {
        $this->open('/vps/test/vps_form_multi-checkbox_test');
        $this->waitForConnections();
    }

}
