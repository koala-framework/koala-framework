<?php
/**
 * @group slow
 * @group selenium
 * @group Kwc_FormDynamic
 * @group Kwc_Trl
 *
 * http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_FormDynamic_Root/de/test1
 * http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_FormDynamic_Root/en/test1
 */
class Kwc_Trl_FormDynamic_Test extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_Trl_FormDynamic_Root');
    }

    public function testTextField()
    {
        $this->openKwc('/de/test1');
        $this->openKwc('/en/test1');
    }
}
