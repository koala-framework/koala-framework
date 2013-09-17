<?php
class Kwf_Component_Cache_PreviewMode_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_PreviewMode_Root');
    }

    public function testIt()
    {
        Kwf_Component_Data_Root::setShowInvisible(true);
        $t = $this->_root->getChildComponent('_test');
        $html = $t->render();
        $this->assertContains('foo', $html);

        Kwf_Component_Data_Root::setShowInvisible(false);
        $html = $t->render();
        $this->assertContains('bar', $html);
    }

    public function testMaster()
    {
        Kwf_Component_Data_Root::setShowInvisible(true);
        $t = $this->_root->getChildComponent('_test');
        $html = $t->render(null, true);
        $this->assertContains('foo', $html);

        Kwf_Component_Data_Root::setShowInvisible(false);
        $html = $t->render(null, true);
        $this->assertContains('bar', $html);
    }
}
