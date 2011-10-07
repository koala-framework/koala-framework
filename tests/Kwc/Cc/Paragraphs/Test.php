<?php
/**
 * @group Cc
 * @group Paragraphs_Cc
 * http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Cc_Paragraphs_Root/master/paragraphs
 * http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Cc_Paragraphs_Root/slave/paragraphs
 */
class Kwc_Cc_Paragraphs_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Cc_Paragraphs_Root');
        $model = Kwf_Model_Abstract::getInstance('Kwc_Cc_Paragraphs_Master_Paragraphs_TestModel');
        $model->getProxyModel()->setData(array(
            array('id' => 1, 'component_id'=>'root-master_paragraphs', 'pos'=>1, 'visible' => 1, 'component' => 'simple'),
            array('id' => 2, 'component_id'=>'root-master_paragraphs', 'pos'=>2, 'visible' => 1, 'component' => 'simple'),
        ));
    }

    public function testContents()
    {
        $domain = Zend_Registry::get('config')->server->domain;

        $c = $this->_root->getPageByUrl('http://'.$domain.'/master/paragraphs', 'en');
        $this->assertEquals($c->componentId, 'root-master_paragraphs');
        $this->assertTrue(substr_count($c->render(), 'simple')==2);

        $c = $this->_root->getPageByUrl('http://'.$domain.'/slave/paragraphs', 'en');
        $this->assertEquals($c->componentId, 'root-slave_paragraphs');
        $html = $c->render();
        $this->assertTrue(substr_count($html, 'simple')==2);
        $this->assertContains('root-slave_paragraphs-1', $html);
        $this->assertContains('root-slave_paragraphs-2', $html);
    }

    public function testClearCacheOnVisibilityChange()
    {
        $this->markTestIncomplete('eventscache');

        $c = $this->_root->getComponentById('root-slave_paragraphs');
        $html = $c->render(); //cache it
        $this->assertEquals(2, substr_count($html, 'simple'));

        $model = Kwf_Model_Abstract::getInstance('Kwc_Cc_Paragraphs_Master_Paragraphs_TestModel');
        $r = $model->getRow('1');
        $r->visible = 0;
        $r->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-slave_paragraphs');
        $html = $c->render();
        $this->assertEquals(1, substr_count($html, 'simple'));
    }

    public function testClearCacheOnAddRow()
    {
        $this->markTestIncomplete('eventscache');

        $c = $this->_root->getComponentById('root-slave_paragraphs');
        $html = $c->render(); //cache it
        $this->assertEquals(2, substr_count($html, 'simple'));

        $model = Kwf_Model_Abstract::getInstance('Kwc_Cc_Paragraphs_Master_Paragraphs_TestModel');
        $r = $model->createRow();
        $r->component_id = 'root-master_paragraphs';
        $r->component = 'simple';
        $r->visible = 1;
        $r->save();

        $this->_process();
        $c = $this->_root->getComponentById('root-slave_paragraphs');
        $html = $c->render();
        $this->assertEquals(3, substr_count($html, 'simple'));
    }

    public function testClearCacheOnRemoveRow()
    {
        $this->markTestIncomplete('eventscache');

        $c = $this->_root->getComponentById('root-slave_paragraphs');
        $html = $c->render(); //cache it
        $this->assertEquals(2, substr_count($html, 'simple'));

        $model = Kwf_Model_Abstract::getInstance('Kwc_Cc_Paragraphs_Master_Paragraphs_TestModel');
        $r = $model->getRow('1');
        $r->delete();

        $this->_process();
        $c = $this->_root->getComponentById('root-slave_paragraphs');
        $html = $c->render();
        $this->assertEquals(1, substr_count($html, 'simple'));
    }
}
