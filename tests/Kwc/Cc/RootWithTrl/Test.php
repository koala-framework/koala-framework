<?php
/**
 * @group Cc
 * @group Cc_RootWithTrl
 */
class Kwc_Cc_RootWithTrl_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Cc_RootWithTrl_Root');
    }

    public function testPages()
    {
        $root = Kwf_Component_Data_Root::getInstance();
        $this->assertEquals(2, count($root->getChildComponents()));
        $this->assertNotNull($root->getComponentById('root-master-master'));
        $this->assertNotNull($root->getComponentById('root-master-slave'));
        $this->assertNotNull($root->getComponentById('root-slave-master'));
        $this->assertNotNull($root->getComponentById('root-slave-slave'));
        $this->assertNotNull($root->getComponentById('1'));
        $this->assertNotNull($root->getComponentById('root-master-slave-main_1'));
        $this->assertNotNull($root->getComponentById('root-slave-master-main_1'));
        $this->assertNotNull($root->getComponentById('root-slave-slave-main_1'));
    }

    public function testHome()
    {
        $root = Kwf_Component_Data_Root::getInstance();
        $this->assertEquals('Kwf_Component_Data_Home', get_class($root->getComponentById('1')));
        $this->assertEquals('Kwf_Component_Data_Home', get_class($root->getComponentById('root-master-slave-main_1')));
        $this->assertEquals('Kwf_Component_Data_Home', get_class($root->getComponentById('root-slave-master-main_1')));
        $this->assertEquals('Kwf_Component_Data_Home', get_class($root->getComponentById('root-slave-slave-main_1')));
        $this->assertEquals('/kwf/kwctest/Kwc_Cc_RootWithTrl_Root/master/master', $root->getComponentById('1')->url);
        $this->assertEquals('/kwf/kwctest/Kwc_Cc_RootWithTrl_Root/master/slave', $root->getComponentById('root-master-slave-main_1')->url);
        $this->assertEquals('/kwf/kwctest/Kwc_Cc_RootWithTrl_Root/slave/master', $root->getComponentById('root-slave-master-main_1')->url);
        $this->assertEquals('/kwf/kwctest/Kwc_Cc_RootWithTrl_Root/slave/slave', $root->getComponentById('root-slave-slave-main_1')->url);
    }

    public function testChildPages()
    {
        $root = Kwf_Component_Data_Root::getInstance();
        $this->assertEquals(3, count($root->getComponentById('root-master-master-main')->getChildPages()));
        $this->assertEquals(2, count($root->getComponentById('root-master-slave-main')->getChildPages()));
        $this->assertEquals(3, count($root->getComponentById('root-slave-master-main')->getChildPages()));
        $this->assertEquals(2, count($root->getComponentById('root-slave-slave-main')->getChildPages()));
        $this->assertEquals('3_trl', $root->getComponentById('root-master-slave-main_3')->filename);
    }

    public function testHistory()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Cc_RootWithTrl_Master_Master_Category_Trl_Model');
        $page = Kwf_Component_Data_Root::getInstance()->getComponentById('root-master-slave-main');
        $this->assertEquals('root-master-slave-main_3', $page->getChildComponent(array('filename' => '3_trl'))->componentId);

        $row = $model->getRow('root-master-slave-main_3');
        $row->name = 'foo';
        $row->save();
        $this->_process();
        $page = Kwf_Component_Data_Root::getInstance()->getComponentById('root-master-slave-main');
        $this->assertEquals('root-master-slave-main_3', $page->getChildComponent(array('filename' => 'foo'))->componentId);
        $this->assertEquals('root-master-slave-main_3', $page->getChildComponent(array('filename' => '3_trl'))->componentId);

        $row = $model->createRow(array(
            'component_id'=>'root-master-slave-main_1', 'name' => '3_Trl', 'filename' => '3_trl', 'visible' => '1', 'custom_filename' => '0'
        ));
        $row->save();
        $this->_process();
        $page = Kwf_Component_Data_Root::getInstance()->getComponentById('root-master-slave-main');
        $this->assertEquals('root-master-slave-main_1', $page->getChildComponent(array('filename' => '3_trl'))->componentId);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Cc_RootWithTrl_Master_Master_Category_PagesModel');
        $row = $model->getRow(1);
        $row->delete();
        $this->_process();
        $page = Kwf_Component_Data_Root::getInstance()->getComponentById('root-master-slave-main');
        $this->assertNull($page->getChildComponent(array('filename' => '3_trl')));
    }
}
