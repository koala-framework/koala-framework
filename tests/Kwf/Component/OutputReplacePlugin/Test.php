<?php
class Kwf_Component_OutputReplacePlugin_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_OutputReplacePlugin_Root_Component');
    }

    public function testIt()
    {
        $html = $this->_root->getComponentById('root_test')->render();
        $this->assertEquals('TestComponent', $html);

        $html = $this->_root->getComponentById('root_test2')->render();
        $this->assertEquals('replacement', $html);
    }
}
