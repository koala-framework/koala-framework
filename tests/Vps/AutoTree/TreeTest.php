<?php
/**
 * @group AutoTree
 * @group slow
 *
 * /vps/test/vps_auto-tree_basic
 */
class Vps_AutoTree_TreeTest extends Vps_Test_SeleniumTestCase
{
    public function testAutoTree()
    {
        $this->open('/vps/test/vps_auto-tree_basic');
        $this->waitForConnections();
        $this->assertTextPresent('p1');
        $this->assertTextPresent('p2');
    }

}