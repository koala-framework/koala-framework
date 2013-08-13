<?php
/**
 * @group Kwc_Trl
 * @group Kwc_Trl_LinkTag
 *
ansicht frontend:
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_LinkTag_Root/de/test1
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_LinkTag_Root/de/test2
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_LinkTag_Root/en/test1
http://doleschal.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_LinkTag_Root/en/test2

DE bearbeiten:
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_LinkTag_Root/Kwc_Trl_LinkTag_LinkTag_Component?componentId=root-master_test1
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_LinkTag_Root/Kwc_Trl_LinkTag_LinkTag_Component?componentId=root-master_test2
EN bearbeiten
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_LinkTag_Root/Kwc_Basic_LinkTag_Trl_Component.Kwc_Trl_LinkTag_LinkTag_Component?componentId=root-en_test1
http://doleschal.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_LinkTag_Root/Kwc_Basic_LinkTag_Trl_Component.Kwc_Trl_LinkTag_LinkTag_Component?componentId=root-en_test2
 */
class Kwc_Trl_LinkTag_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_LinkTag_Root');
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test1');
        $this->assertEquals('', $c->render());

        $c = $this->_root->getComponentById('root-master_test2');
        $this->assertRegExp('#<a .*?href="http://www.vivid-planet.com/">#', $c->render());
    }

    public function testEn()
    {
        //$c = $this->_root->getComponentById('root-en_test1');
        //$this->assertEquals('', $c->render());

        $c = $this->_root->getComponentById('root-en_test2');
        $this->assertRegExp('#<a .*?href="http://www.vivid-planet.com/en">#', $c->render());
    }

    public function testCacheEn()
    {
        $c = $this->_root->getComponentById('root-master_test3');
        $this->assertEquals('', $c->render());
        $c = $this->_root->getComponentById('root-en_test3');
        $this->assertEquals('', $c->render());

        $model = Kwf_Model_Abstract::getInstance('Kwc_Trl_LinkTag_LinkTag_TestModel');
        $r = $model->getRow('root-master_test3');
        $r->component = 'extern';
        $r->save();

        $this->_process();
        $c = $this->_root->getComponentById('root-master_test3');
        $this->assertEquals('', $c->render());
        $c = $this->_root->getComponentById('root-en_test3');
        $this->assertEquals('', $c->render());

        $model = Kwf_Model_Abstract::getInstance('Kwc_Trl_LinkTag_LinkTag_Extern_TestModel');
        $model->createRow(array(
            'component_id'=>'root-master_test3-child',
            'target'=>'http://www.test.de/',
            'open_type'=>'self'
        ))->save();

        $this->_process();
        $c = $this->_root->getComponentById('root-master_test3');
        $this->assertRegExp('#<a .*?href="http://www.test.de/">#', $c->render());
        $c = $this->_root->getComponentById('root-en_test3');
        $this->assertEquals('', $c->render());

        $model = Kwf_Model_Abstract::getInstance('Kwc_Trl_LinkTag_LinkTag_Extern_Trl_TestModel');
        $model->createRow(array(
            'component_id'=>'root-en_test3-child',
            'target'=>'http://www.test.de/en',
            'own_target' => true
        ))->save();

        $this->_process();
        $c = $this->_root->getComponentById('root-master_test3');
        $this->assertRegExp('#<a .*?href="http://www.test.de/">#', $c->render());
        $c = $this->_root->getComponentById('root-en_test3');
        $this->assertRegExp('#<a .*?href="http://www.test.de/en">#', $c->render());
    }
}
