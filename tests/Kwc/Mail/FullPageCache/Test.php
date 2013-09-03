<?php
class Kwc_Mail_FullPageCache_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Mail_FullPageCache_Root_Component');
    }

    public function testIt()
    {
        //doesn't use fullPage cache (as it's not a page)
        $c = $this->_root->getComponentById('root-testMail1');
        $html = $c->getComponent()->getHtml();
        $this->assertContains('<p>abcd</p>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Mail_FullPageCache_TestMail_Html_Model')
            ->getRow('root-testMail1-content');
        $row->content = '<p>1234</p>';
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-testMail1');
        $html = $c->getComponent()->getHtml();
        $this->assertContains('<p>1234</p>', $html);
    }

    public function testPage()
    {
        //uses fullPage cache
        $c = $this->_root->getComponentById('root_testMail2');
        $html = $c->getComponent()->getHtml();
        $this->assertContains('<p>abcd</p>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Mail_FullPageCache_TestMail_Html_Model')
            ->getRow('root_testMail2-content');
        $row->content = '<p>1234</p>';
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root_testMail2');
        $html = $c->getComponent()->getHtml();
        $this->assertContains('<p>1234</p>', $html);
    }
}
