<?php
// dient zB auch dem Vpc_Basic_LinkTagNews test
/**
 * @group Vpc_News
 */
class Vpc_News_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_News_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testBasic()
    {
        $newsDir = $this->_root->getComponentById(2100);
        $this->assertEquals('/newsbar1', $newsDir->url);

        $newsDetail = $newsDir->getChildComponent('_501');
        $this->assertEquals('/newsbar1/501_a', $newsDetail->url);
    }
}
