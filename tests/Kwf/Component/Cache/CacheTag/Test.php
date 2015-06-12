<?php
class Kwf_Component_Cache_CacheTag_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_CacheTag_Root');
    }

    public function testIt()
    {
        $t = $this->_root->getChildComponent('_test');
        $html = $t->render();
        $this->assertContains('foo', $html);

        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_CacheTag_Test_Model')->getRow(1);
        $r->test = 'bar';
        $r->save();
        $this->_process();

        $html = $t->render();
        $this->assertContains('bar', $html);
    }
}
