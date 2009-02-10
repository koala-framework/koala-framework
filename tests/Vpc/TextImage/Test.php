<?php
/**
 * @group Vpc_TextImage
 */
class Vpc_TextImage_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_TextImage_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
        parent::setUp();
    }

    public function testIt()
    {
        
        // http://prosalzburg.vps.niko.vivid/vps/componentedittest/Vpc_TextImage_Root/Vpc_TextImage_TestComponent?componentId=root_textImage1
        // http://prosalzburg.vps.niko.vivid/vps/vpctest/Vpc_TextImage_Root/textimage1
    }
}
