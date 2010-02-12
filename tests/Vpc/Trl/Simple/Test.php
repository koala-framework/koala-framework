<?php
/**
 * @group Vpc_Trl
 * @group Vpc_UrlResolve
 */
class Vpc_Trl_Simple_Text extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_Simple_Root');
    }
    public function testIt()
    {
        //$this->assertEquals('root-de_test', $this->_root->getPageByUrl('/de/test')->componentId);
    }
/*
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_Simple_Root/de/test
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_Simple_Root/en/test
*/
}
?>