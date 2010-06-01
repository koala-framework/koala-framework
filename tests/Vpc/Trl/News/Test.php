<?php
/**
 * @group Vpc_Trl
 * @group Vpc_Trl_News
 *
ansicht frontend:
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_News_Root/de/test
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_News_Root/en/test

http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_News_Root/Vpc_Trl_News_News_Component?componentId=root-master_test
http://doleschal.vps.niko.vivid/vps/componentedittest/Vpc_Trl_News_Root/Vpc_Trl_News_News_Trl_Component.Vpc_Trl_News_News_Component?componentId=root-en_test

*/
class Vpc_Trl_News_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_News_Root');
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
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render(); //cache it
        $this->assertEquals(1, substr_count($html, 'href='));
        $vars = $c->getGenerator('detail')->getCacheVars($c);

        $model = Vps_Model_Abstract::getInstance('Vpc_Trl_News_News_Trl_TestModel');
        $r = $model->getRow('root-en_test_1');
        $r->visible = 0;
        $r->save();

        $this->_process();
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render();
        $this->assertEquals(0, substr_count($html, 'href='));
    }
}
