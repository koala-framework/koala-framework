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
        $this->assertContains('/en/test/1_loremen', $c->render());
    }

    public function testCacheEnOnVisibleChange()
    {
        $this->markTestIncomplete('eventscache');

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
}
