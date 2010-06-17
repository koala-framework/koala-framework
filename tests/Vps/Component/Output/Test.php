<?php
/**
 * @group Component_Output
 */
class Vps_Component_Output_Test extends PHPUnit_Framework_TestCase
{
    public function testMaster()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_C3_Root_Component');
        $root = Vps_Component_Data_Root::getInstance();
        $output = new Vps_Component_View();

        $value = $output->renderMaster($root);
        $this->assertEquals('c3_rootmaster c1_box c3_root', $value);

        $value = $output->renderMaster($root->getChildComponent('_childpage'));
        $this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage', $value);

        $value = $output->renderMaster($root->getChildComponent('_childpage')->getChildComponent('_childpage'));
        $this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage2', $value);
    }

    public function testC1()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_C1_Root_Component');
        $root = Vps_Component_Data_Root::getInstance();
        $output = new Vps_Component_View();

        $value = $output->render($root->getChildComponent('-child'));
        $this->assertEquals('c1_childmaster c1_child c1_childchild', $value);

        $value = $output->renderMaster($root);
        $this->assertEquals('c1_rootmaster c1_box c1_root plugin(plugin(c1_childmaster c1_child c1_childchild))', $value);

        $value = $output->render($root);
        $this->assertEquals('c1_root plugin(plugin(c1_childmaster c1_child c1_childchild))', $value);
    }

    public function testC3()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_C3_Root_Component');
        $root = Vps_Component_Data_Root::getInstance();
        $output = new Vps_Component_View();

        $value = $output->renderMaster($root);
        $this->assertEquals('c3_rootmaster c1_box c3_root', $value);

        $value = $output->renderMaster($root->getChildComponent('_childpage'));
        $this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage', $value);

        $value = $output->render($root->getChildComponent('_childpage'));
        $this->assertEquals('c3_childpagemaster c3_childpage', $value);

        $value = $output->renderMaster($root->getChildComponent('_childpage')->getChildComponent('_childpage'));
        $this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage2', $value);
    }

    public function testPlugin()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_Plugin_Component');
        $root = Vps_Component_Data_Root::getInstance();
        $output = new Vps_Component_View();

        $value = $output->renderMaster($root);
        // Eigentlicher Code zur Kontrolle in PluginAfter!
        $this->assertEquals('rootmaster pluginChild rootmasterend', $value);
    }

    public function testPartial()
    {
        $this->markTestIncomplete();
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
        $this->markTestIncomplete();
        $output = new Vps_Component_Output_NoCache();

        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_Dynamic_Component');
        $value = $output->renderMaster(Vps_Component_Data_Root::getInstance());
        $this->assertEquals('dynamic bar2foo', $value);
    }

    public function testHasContent()
    {
        $this->markTestIncomplete();
        $output = new Vps_Component_Output_NoCache();

        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_HasContent_Component');
        $value = $output->renderMaster(Vps_Component_Data_Root::getInstance());
        $this->assertEquals('root child2', $value);
    }
}