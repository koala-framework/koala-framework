<?php
/**
 * @group Component_RootPlugin
 */
class Kwf_Component_RootPlugin_Test extends Kwc_TestAbstract
{
    public function testPostRender()
    {
        $this->_init('Kwf_Component_RootPlugin_PostRender_Component');
        $this->assertEquals('foo', $this->_renderRoot());
        Kwf_Component_Data_Root::getInstance()->registerPlugin(new Kwf_Component_RootPlugin_PostRender_Plugin());
        $this->assertEquals('foobar', $this->_renderRoot());
    }

    public function testMaskComponentLink()
    {
        $this->_init('Kwf_Component_RootPlugin_MaskComponentLink_Component');
        $this->assertEquals('f1f2f3', $this->_renderRoot());

        Kwf_Component_Data_Root::getInstance()->registerPlugin(new Kwf_Component_RootPlugin_MaskComponentLink_Plugin());
        $this->assertEquals('f1f3', $this->_renderRoot());

        Kwf_Component_Data_Root::getInstance()->registerPlugin(new Kwf_Component_RootPlugin_MaskComponentLink_Plugin2());
        $this->assertEquals('f1f2', $this->_renderRoot());
    }

    private function _renderRoot()
    {
        // remove kwfMainContent-Divs
        return str_replace(array("\n", ' '), '', strip_tags($this->_root->render(false, true)));
    }
}
