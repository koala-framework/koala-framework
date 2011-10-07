<?php
/**
 * @group slow
 * @group selenium
 * @group AutoTree
 */
class Vps_AutoTree_Test extends Vps_Test_SeleniumTestCase
{
    public function testAutoTree()
    {
        $this->open('/vps/test/vps_auto-tree_basic');
        $this->waitForConnections();
        $this->assertTextPresent('p1');
        $this->assertTextPresent('p2');
    }
}