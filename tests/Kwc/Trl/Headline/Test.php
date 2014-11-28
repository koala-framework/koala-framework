<?php
class Kwc_Trl_Headline_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Headline_Root');
    }

    public function testDeClearCache()
    {
        $c = $this->_root->getComponentById('root-master_headline');
        $html = $c->render(true, false);
        $this->assertRegExp('#<h1>\s*<span>\s*foo\s*</span>\s*</h1>#', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_Headline_Headline_Model')->getRow('root-master_headline');
        $row->headline1 = 'foo1';
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-master_headline');
        $html = $c->render(true, false);
        $this->assertRegExp('#<h1>\s*<span>\s*foo1\s*</span>\s*</h1>#', $html);
    }

    public function testEnClearCache()
    {
        $c = $this->_root->getComponentById('root-en_headline');
        $html = $c->render(true, false);
        $this->assertRegExp('#<h1>\s*<span>\s*fooen\s*</span>\s*</h1>#', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_Headline_Headline_Trl_Model')->getRow('root-en_headline');
        $row->headline1 = 'fooen1';
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_headline');
        $html = $c->render(true, false);
        $this->assertRegExp('#<h1>\s*<span>\s*fooen1\s*</span>\s*</h1>#', $html);
    }
}
