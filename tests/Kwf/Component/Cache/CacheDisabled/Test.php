<?php
class Kwf_Component_Cache_CacheDisabled_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_CacheDisabled_Root');
    }

    public function testIt()
    {
        $t = $this->_root->getChildComponent('_test');
        $html = $t->render(true, true);
        $this->assertContains('foo', $html);

        Kwf_Component_Cache_CacheDisabled_Test_Component::$test = 'bar';
        //don't process or something, viewCache is disabled

        $html = $t->render(true, true);
        $this->assertContains('bar', $html);
    }
}
