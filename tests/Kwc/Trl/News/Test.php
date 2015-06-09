<?php
/**
 * @group Kwc_Trl
 * @group Kwc_Trl_News
 *
ansicht frontend:
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_News_Root/de/test
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_News_Root/en/test

http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_News_Root/Kwc_Trl_News_News_Component?componentId=root-master_test
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_News_Root/Kwc_Trl_News_News_Trl_Component.Kwc_Trl_News_News_Component?componentId=root-en_test

*/
class Kwc_Trl_News_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_News_Root');
        $trlElements = array();
        $trlElements['kwf']['de'] = array();
        Kwf_Trl::getInstance()->setTrlElements($trlElements);
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test');
        $html = $c->render();
        $this->assertEquals(2, substr_count($html, 'href='));
        $this->assertContains('/de/test/1_lipsum', $html);
        $this->assertContains('/de/test/2_lipsum2', $html);
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render();
        $this->assertEquals(1, substr_count($html, 'href='));
        $this->assertContains('/en/test/1_loremen', $html);
    }

    public function testCacheEnOnVisibleChange()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render(); //cache it
        $this->assertEquals(1, substr_count($html, 'href='));

        $model = Kwf_Model_Abstract::getInstance('Kwc_Trl_News_News_Trl_TestModel');
        $r = $model->getRow('root-en_test_1');
        $r->visible = false;
        $r->save();

        $this->_process();
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render();
        $this->assertEquals(0, substr_count($html, 'href='));
    }

    public function testCacheEnOnDelete()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render(); //cache it
        $this->assertEquals(1, substr_count($html, 'href='));

        $model = Kwf_Model_Abstract::getInstance('Kwc_Trl_News_News_TestModel');
        $r = $model->getRow(1);
        $r->delete();

        $this->_process();
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render();
        $this->assertEquals(0, substr_count($html, 'href='));
    }

    public function testCacheEnOnChangeTitle()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render(); //cache it
        $this->assertContains('loremen', $html);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Trl_News_News_Trl_TestModel');
        $r = $model->getRow('root-en_test_1');
        $r->title = 'foobaren';
        $r->save();

        $this->_process();
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render();
        $this->assertContains('foobaren', $html);
        $this->assertNotContains('loremen', $html);
    }

    public function testCacheEnOnChangeDate()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render(); //cache it
        $this->assertContains('2010-03-01', $html);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Trl_News_News_TestModel');
        $r = $model->getRow('1');
        $r->publish_date = '2010-03-05';
        $r->save();

        $this->_process();
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render();
        $this->assertContains('2010-03-05', $html);
    }

    public function testDetailCacheEnOnChangeTitle()
    {
        $c = $this->_root->getComponentById('root-en_test_1');
        $html = $c->render(); //cache it
        $this->assertContains('loremen', $html);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Trl_News_News_Trl_TestModel');
        $r = $model->getRow('root-en_test_1');
        $r->title = 'foobaren';
        $r->save();

        $this->_process();
        $c = $this->_root->getComponentById('root-en_test_1');
        $html = $c->render();
        $this->assertContains('foobaren', $html);
    }

    public function testDetailCacheEnOnChangeDate()
    {
        $c = $this->_root->getComponentById('root-en_test_1');
        $html = $c->render(); //cache it
        $this->assertContains('2010-03-01', $html);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Trl_News_News_TestModel');
        $r = $model->getRow('1');
        $r->publish_date = '2010-03-05';
        $r->save();

        $this->_process();
        $c = $this->_root->getComponentById('root-en_test_1');
        $html = $c->render();
        $this->assertContains('2010-03-05', $html);
    }
}
