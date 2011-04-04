<?
/**
 * @group Vpc_Trl
 * @group Vpc_Trl_Columns
 *
http://cultourseurope.vps.niko.vivid/vps/vpctest/Vpc_Trl_Columns_Root/de/test
http://cultourseurope.vps.niko.vivid/vps/vpctest/Vpc_Trl_Columns_Root/en/test

http://cultourseurope.vps.niko.vivid/vps/componentedittest/Vpc_Trl_Columns_Root/Vpc_Trl_Columns_Columns_Component?componentId=root-master_test
http://cultourseurope.vps.niko.vivid/vps/componentedittest/Vpc_Trl_Columns_Root/Vpc_Columns_Trl_Component.Vpc_Trl_Columns_Columns_Component?componentId=root-en_test
*/
class Vpc_Trl_Columns_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_Columns_Root');
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
