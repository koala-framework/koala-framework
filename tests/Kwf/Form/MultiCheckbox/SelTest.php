<?php
/**
 * @group slow
 * @group Kwf_Form_MultiCheckbox
 */
class Kwf_Form_MultiCheckbox_Test extends Kwf_Test_SeleniumTestCase
{
    public function test()
    {
        $this->open('/kwf/test/kwf_form_multi-checkbox_test');
        $this->waitForConnections();
    }

}
