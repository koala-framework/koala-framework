<?php
/**
 * @group Kwc_Trl
 *
ansicht frontend:
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_Paragraphs_Root/de/test
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_Paragraphs_Root/en/test

DE bearbeiten:
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_Paragraphs_Root/Kwc_Trl_Paragraphs_Paragraphs_Component?componentId=root-de_test
EN bearbeiten
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_Paragraphs_Root/Kwc_Trl_Paragraphs_Paragraphs_Trl_Component.Kwc_Trl_Paragraphs_Paragraphs_Component/?componentId=root-en_test
 */
class Kwc_Trl_Paragraphs_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Paragraphs_Root');
    }
    public function testIt()
    {
        $domain = Zend_Registry::get('config')->server->domain;

        $c = $this->_root->getPageByUrl('http://'.$domain.'/de/test', 'en');
        $this->assertEquals($c->componentId, 'root-master_test');
        $this->assertTrue(substr_count($c->render(), 'child (de)')==4);

        $c = $this->_root->getPageByUrl('http://'.$domain.'/en/test', 'en');
        $this->assertEquals($c->componentId, 'root-en_test');
        $this->assertTrue(substr_count($c->render(), 'child (en)')==2);
    }

    public function testCacheEnChangeStatusShow()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $this->assertTrue(substr_count($c->render(), 'child (en)')==2);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_Paragraphs_Paragraphs_Trl_TestModel')->getRow('root-en_test-2');
        $row->visible = true;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_test');
        $this->assertTrue(substr_count($c->render(), 'child (en)')==3);
    }

    public function testCacheEnChangeStatusShowNonExistingTrlRow()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $this->assertTrue(substr_count($c->render(), 'child (en)')==2);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_Paragraphs_Paragraphs_Trl_TestModel')->createRow();
        $row->component_id = 'root-en_test-3';
        $row->visible = true;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_test');
        $this->assertTrue(substr_count($c->render(), 'child (en)')==3);
    }

    public function testCacheEnChangeStatusHide()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $this->assertTrue(substr_count($c->render(), 'child (en)')==2);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_Paragraphs_Paragraphs_Trl_TestModel')->getRow('root-en_test-1');
        $row->visible = false;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_test');
        $this->assertTrue(substr_count($c->render(), 'child (en)')==1);
    }

    public function testCacheEnChangeOrder()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render();
        $this->assertTrue(substr_count($html, 'child (en)')==2);
        $this->assertTrue(!!preg_match('#child \(en\) root-en_test-1.*child \(en\) root-en_test-4#s', $html));

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_Paragraphs_Paragraphs_TestModel')->getRow('4');
        $row->pos = 1;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render();
        $this->assertTrue(substr_count($html, 'child (en)')==2);
        $this->assertTrue(!!preg_match('#child \(en\) root-en_test-4.*child \(en\) root-en_test-1#s', $html));
    }

    public function testCacheEnDelete()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render();
        $this->assertTrue(substr_count($html, 'child (en)')==2);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_Paragraphs_Paragraphs_TestModel')->getRow('4');
        $row->delete();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_test');
        $this->assertTrue(substr_count($c->render(), 'child (en)')==1);
    }
}
