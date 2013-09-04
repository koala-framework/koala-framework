<?php
class Kwf_Component_OutputReplacePlugin_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_OutputReplacePlugin_Root_Component');
    }

    public function testNotReplace()
    {
        $html = $this->_root->getComponentById('root_test')->render();
        $this->assertEquals('TestComponent', $html);

        //re-render, now cached
        $html = $this->_root->getComponentById('root_test')->render();
        $this->assertEquals('TestComponent', $html);
    }

    public function testReplace()
    {
        $html = $this->_root->getComponentById('root_test2')->render();
        $this->assertEquals('replacement', $html);

        //re-render, now cached
        $html = $this->_root->getComponentById('root_test2')->render();
        $this->assertEquals('replacement', $html);
    }

    public function testNotReplaceMaster()
    {
        $html = $this->_root->getComponentById('root_test')->render(true, true);
        $this->assertContains('TestComponent', $html);

        //re-render, now cached
        $html = $this->_root->getComponentById('root_test')->render(true, true);
        $this->assertContains('TestComponent', $html);
    }

    public function testReplaceMaster()
    {
        $html = $this->_root->getComponentById('root_test2')->render(true, true);
        $this->assertContains('replacement', $html);

        //re-render, now cached
        $html = $this->_root->getComponentById('root_test2')->render(true, true);
        $this->assertContains('replacement', $html);
    }

}
