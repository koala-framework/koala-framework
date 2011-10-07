<?php
// dient zB auch dem Kwc_Basic_LinkTagEvent test
/**
 * @group Kwc_Events
 */
class Kwc_Events_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Events_Root');
        $this->_root->setFilename(null);
    }

    public function testBasic()
    {
        $eventsDir = $this->_root->getComponentById(3100);
        $this->assertEquals('/events1', $eventsDir->url);

        $eventDetail = $eventsDir->getChildComponent('_601');
        $this->assertEquals('/events1/601_a', $eventDetail->url);
    }
}
