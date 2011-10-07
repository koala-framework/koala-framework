<?php
/**
 * @group slow
 * @group selenium
 * @group AutoTree
 */
class Kwf_AutoTree_Test extends Kwf_Test_SeleniumTestCase
{
    public function testAutoTree()
    {
        $this->open('/kwf/test/kwf_auto-tree_basic');
        $this->waitForConnections();
        $this->assertTextPresent('p1');
        $this->assertTextPresent('p2');
    }
}