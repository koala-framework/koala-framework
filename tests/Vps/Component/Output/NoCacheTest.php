<?php
/**
 * @group Component_Output_NoCache
 */
class Vps_Component_Output_NoCacheTest extends Vps_Test_TestCase
{
    public function testMaster()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_C3_Root_Component');
        $root = Vps_Component_Data_Root::getInstance();
        $output = new Vps_Component_Output_Master();

        $value = $output->render($root);
        $this->assertEquals('master {nocache: Vps_Component_Output_C1_Box_Component root-box } {nocache: Vps_Component_Output_C3_Root_Component root }', $value);

        $value = $output->render($root->getChildComponent('_childpage'));
        $this->assertEquals('master {nocache: Vps_Component_Output_C3_Box_Component root_childpage-box } {nocache: Vps_Component_Output_C3_ChildPage_Component root_childpage }', $value);

        $value = $output->renderMaster($root->getChildComponent('_childpage')->getChildComponent('_childpage'));
        $this->assertEquals('master {nocache: Vps_Component_Output_C3_Box_Component root_childpage_childpage-box } master2 {nocache: Vps_Component_Output_C3_ChildPage2_Component root_childpage_childpage }', $value);
    }

    public function testC1()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_C1_Root_Component');
        $root = Vps_Component_Data_Root::getInstance();
        $output = new Vps_Component_Output_NoCache();

        $value = $output->render($root->getChildComponent('-child'));
        $this->assertEquals('master2 child child2', $value);

        $value = $output->renderMaster($root);
        $this->assertEquals('master box root plugin(plugin(master2 child child2))', $value);

        $value = $output->render($root);
        $this->assertEquals('root plugin(plugin(master2 child child2))', $value);
    }

    public function testC3()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_C3_Root_Component');
        $root = Vps_Component_Data_Root::getInstance();
        $output = new Vps_Component_Output_NoCache();

        $value = $output->renderMaster($root);
        $this->assertEquals('master box root', $value);

        $value = $output->renderMaster($root->getChildComponent('_childpage'));
        $this->assertEquals('master box2 master2 childpage', $value);

        $value = $output->render($root->getChildComponent('_childpage'));
        $this->assertEquals('master2 childpage', $value);

        $value = $output->renderMaster($root->getChildComponent('_childpage')->getChildComponent('_childpage'));
        $this->assertEquals('master box2 master2 childpage2', $value);
    }

    public function testPartial()
    {
        $output = new Vps_Component_Output_NoCache();

        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_Partial_Random_Component');
        $value = $output->renderMaster(Vps_Component_Data_Root::getInstance());
        $this->assertTrue(in_array($value, array(
            'bar0bar1', 'bar0bar2', 'bar1bar2', 'bar1bar0', 'bar2bar0', 'bar2bar1'
        )));

        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_Partial_Paging_Component');
        $value = $output->renderMaster(Vps_Component_Data_Root::getInstance());
        $this->assertEquals('bar2', $value);
    }

    public function testDynamic()
    {
        $output = new Vps_Component_Output_NoCache();

        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_Dynamic_Component');
        $value = $output->renderMaster(Vps_Component_Data_Root::getInstance());
        $this->assertEquals('dynamic bar2foo', $value);
    }

    public function testPlugin()
    {
        $output = new Vps_Component_Output_NoCache();

        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_Plugin_Component');
        $value = $output->renderMaster(Vps_Component_Data_Root::getInstance());
        // Eigentlicher Code zur Kontrolle in PluginAfter!
        $this->assertEquals('master  pluginChild', $value);
    }

    public function testHasContent()
    {
        $output = new Vps_Component_Output_NoCache();

        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_HasContent_Component');
        $value = $output->renderMaster(Vps_Component_Data_Root::getInstance());
        $this->assertEquals('root child2', $value);
    }
}