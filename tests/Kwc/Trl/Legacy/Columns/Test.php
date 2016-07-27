<?php
/**
 * @group Kwc_Trl
 * @group Kwc_Trl_Columns
 *
http://cultourseurope.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_Legacy_Columns_Root/de/test
http://cultourseurope.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_Legacy_Columns_Root/en/test

http://cultourseurope.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_Legacy_Columns_Root/Kwc_Trl_Legacy_Columns_Columns_Component?componentId=root-master_test
http://cultourseurope.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_Legacy_Columns_Root/Kwc_Columns_Trl_Component.Kwc_Trl_Legacy_Columns_Columns_Component?componentId=root-en_test
*/
class Kwc_Trl_Legacy_Columns_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Legacy_Columns_Root');

        //initialize model content, I don't know why here
        $proxyModel = Kwf_Model_Abstract::getInstance('Kwc_Trl_Legacy_Columns_Columns_Trl_ColumnsTrlModel')->getProxyModel();
        $proxyModel
            ->createRow(array('component_id'=>'root-en_test-1', 'visible' => 1))
            ->save();
        $proxyModel
            ->createRow(array('component_id'=>'root-en_test-2', 'visible' => 1))
            ->save();
        $proxyModel
            ->createRow(array('component_id'=>'root-en_test-3', 'visible' => 1))
            ->save();

    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test');
        $this->assertEquals(3, substr_count($c->render(), 'columnContent'));
        $this->assertContains('root-master_test-2', $c->render());
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $this->assertEquals(3, substr_count($c->render(), 'columnContent'));
        $this->assertContains('root-en_test-2', $c->render());
    }
}
