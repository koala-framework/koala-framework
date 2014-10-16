<?php
class Kwf_Component_RenderTwig_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_RenderTwig_Root_Component');
    }
    public function testIt()
    {
        $c = $this->_root->getComponentById('root-testCmp');
        $html = $c->render();
        $html = str_replace(' ', '', $html);
        $html = str_replace("\n", '', $html);
        $this->assertEquals('abchead2def', $html);
    }
}
