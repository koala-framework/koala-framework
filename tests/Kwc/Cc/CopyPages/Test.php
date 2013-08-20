<?php
class Kwc_Cc_CopyPages_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Cc_CopyPages_Root');
    }

    public function testIt()
    {
        $html = $this->_root->getComponentById('1')->render();
        $this->assertContains("test123", $html);

        $html = $this->_root->getComponentById('2')->render();
        $this->assertContains("test123", $html);

    }
}
