<?php
// dient zB auch dem Vpc_Basic_LinkTagEvent test
/**
 * @group Vpc_Events
 */
class Vpc_Events_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Events_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testBasic()
    {
        $eventsDir = $this->_root->getComponentById(3100);
        $this->assertEquals('/events1', $eventsDir->url);

        $eventDetail = $eventsDir->getChildComponent('_601');
        $this->assertEquals('/events1/601_a', $eventDetail->url);
    }
}
