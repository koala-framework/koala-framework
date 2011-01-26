<?php
// dient zB auch dem Vpc_Basic_LinkTagEvent test
/**
 * @group Vpc_Events
 */
class Vpc_Events_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Events_Root');
    }

    public function testBasic()
    {
        $eventsDir = $this->_root->getComponentById(3100);
        $this->assertEquals('/events1', $eventsDir->url);

        $eventDetail = $eventsDir->getChildComponent('_601');
        $this->assertEquals('/events1/601_a', $eventDetail->url);
    }
}
