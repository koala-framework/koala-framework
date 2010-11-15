<?php
/**
 * @group Directories_Top
 */
class Vpc_Directories_Top_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Directories_Top_Root');
    }

    public function testDetail()
    {
        $dir = $this->_root->getComponentById('root_directory');
        $this->assertNotNull($dir);
        $details = $dir->getChildComponents(array('generator'=>'detail'));
        $this->assertEquals(count($details), 8);
    }

    public function testTop()
    {
        $dir = $this->_root->getComponentById('root_top');
        $this->assertNotNull($dir);
        $partialParams = $dir->getChildComponent('-view')->getComponent()->getPartialParams();
        $this->assertEquals($partialParams['count'], 5);
        $paging = $dir->getChildComponent('-view')->getChildComponent('-paging');
        $this->assertEquals($paging->getComponent()->getCount(), 5);
    }

    public function testCache()
    {
        $c = $this->_root->getChildComponent('_directory');
        $model = $c->getComponent()->getChildModel();

        $this->assertEquals(6, substr_count($c->render(), 'Foo'));

        $row = $model->createRow(
            array('id' => 9, 'name'=>'Foo6', 'component_id'=>'root_directory')
        );
        $row->save();
        $this->_process();
        $c = $this->_root->getChildComponent('_directory');
        $this->assertEquals(7, substr_count($c->render(), 'Foo'));

        // Zeile löschen
        $model->getRow(9)->delete();
        Vps_Component_Data_Root::reset();
        $this->_root = Vps_Component_Data_Root::getInstance();
        $c = $this->_root->getChildComponent('_directory');
        $this->assertEquals(6, substr_count($c->render(), 'Foo'));
    }
}
