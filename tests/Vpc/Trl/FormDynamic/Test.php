<?php
/**
 * @group slow
 * @group selenium
 * @group Vpc_FormDynamic
 * @group Vpc_Trl
 *
 * http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_FormDynamic_Root/de/test1
 * http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_FormDynamic_Root/en/test1
 */
class Vpc_Trl_FormDynamic_Test extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vpc_Trl_FormDynamic_Root');
    }

    public function testTextField()
    {
        $this->openVpc('/de/test1');
        $this->openVpc('/en/test1');
    }
}
