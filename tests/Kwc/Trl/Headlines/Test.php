<?php
class Kwc_Trl_Headlines_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Headlines_Root');
    }

    public function testDeClearCache()
    {
        $c = $this->_root->getComponentById('root-master_headlines');
        $html = $c->render(true, false);
        $this->assertContains('<h1 class="headlineH1">foo</h1>', $html);


        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_Headlines_Headlines_Model')->getRow('root-master_headlines');
        $row->headline1 = 'foo1';
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-master_headlines');
        $html = $c->render(true, false);
        $this->assertContains('<h1 class="headlineH1">foo1</h1>', $html);
    }

    public function testEnClearCache()
    {
        $c = $this->_root->getComponentById('root-en_headlines');
        $html = $c->render(true, false);
        $this->assertContains('<h1 class="headlineH1">fooen</h1>', $html);
        

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_Headlines_Headlines_Trl_Model')->getRow('root-en_headlines');
        $row->headline1 = 'fooen1';
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_headlines');
        $html = $c->render(true, false);
        $this->assertContains('<h1 class="headlineH1">fooen1</h1>', $html);
    }
}
