<?php
/**
 * @group Component_PluginRoot
 */
class Kwf_Component_PluginRoot_Test extends Kwc_TestAbstract
{
    public function testPostRender()
    {
        $this->_init('Kwf_Component_PluginRoot_PostRender_Component');
        $this->assertEquals('foo', $this->_renderRoot());
        Kwf_Component_Data_Root::getInstance()->registerPlugin(new Kwf_Component_PluginRoot_PostRender_Plugin());
        $this->assertEquals('foobar', $this->_renderRoot());
    }

    public function testMaskComponentLink()
    {
        $this->_init('Kwf_Component_PluginRoot_MaskComponentLink_Component');
        $this->assertEquals('f1f2f3', $this->_renderRoot());

        $plugin = new Kwf_Component_PluginRoot_MaskComponentLink_Plugin();
        Kwf_Component_Data_Root::getInstance()->registerPlugin($plugin);
        $this->assertEquals('f1f3', $this->_renderRoot());

        $output = $this->_root->render(false, true);

        $parts = $plugin->getMaskedContentParts($output);
        $this->assertEquals(2, count($parts));
        $this->assertEquals($plugin::MASK_TYPE_HIDE, $parts[0]['maskType']);
        $this->assertEquals($plugin::MASK_TYPE_SHOW, $parts[1]['maskType']);

        $parts = $plugin->getMaskedContentParts($output, $plugin::MASK_TYPE_HIDE);
        $this->assertEquals(1, count($parts));
        $this->assertEquals(array('foo' => 'a'), $parts[0]['params']);

        $parts = $plugin->getMaskedContentParts($output, $plugin::MASK_TYPE_HIDE, array('foo' => 'b'));
        $this->assertEquals(0, count($parts));

        $parts = $plugin->getMaskedContentParts($output, $plugin::MASK_TYPE_SHOW, array('foo' => 'b'));
        $this->assertEquals(1, count($parts));

        $this->assertEquals('f1f2f3', $this->_strip($plugin->removeMasksFromComponentLinks($output)));
        $this->assertEquals('f1f2f3', $this->_strip($plugin->removeMasksFromComponentLinks($output, array('foo' => 'a'))));
        $this->assertEquals('f1f3', $this->_strip($plugin->removeMasksFromComponentLinks($output, '')));
        $this->assertEquals('f1', $this->_strip($plugin->removeMaskedComponentLinks($output)));
        $this->assertEquals('f1f2', $this->_strip($plugin->removeMaskedComponentLinks($plugin->removeMasksFromComponentLinks($output))));
    }

    private function _renderRoot()
    {
        return $this->_strip($this->_root->render(false, true));
    }

    private function _strip($output)
    {
        // remove kwfMainContent-Divs
        return str_replace(array("\n", ' '), '', strip_tags($output));
    }
}
