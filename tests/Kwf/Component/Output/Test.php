<?php
/**
 * @group Component_Output
 */
class Kwf_Component_Output_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Registry::get('config')->debug->componentCache->disable = true;
    }

    public function testMaster()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_Output_C3_Root_Component');
        $root = Kwf_Component_Data_Root::getInstance();
        $view = new Kwf_Component_Renderer();

        //$value = $view->renderMaster($root);
        //$this->assertEquals('c3_rootmaster c1_box c3_root', $value);

        $value = $view->renderMaster($root->getChildComponent('_childpage'));
        $this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage', $value);

        //$value = $view->renderMaster($root->getChildComponent('_childpage')->getChildComponent('_childpage'));
        //$this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage2', $value);
    }

    public function testC1()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_Output_C1_Root_Component');
        $root = Kwf_Component_Data_Root::getInstance();
        $view = new Kwf_Component_Renderer();

        $value = $view->renderComponent($root->getChildComponent('-child'));
        $this->assertEquals('plugin(plugin(c1_child c1_childchild))', $value);

        $value = $view->renderMaster($root);
        $this->assertEquals('c1_rootmaster c1_box c1_root plugin(plugin(c1_child c1_childchild))', $value);

        $value = $view->renderComponent($root);
        $this->assertEquals('c1_root plugin(plugin(c1_child c1_childchild))', $value);
    }

    public function testC3()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_Output_C3_Root_Component');
        $root = Kwf_Component_Data_Root::getInstance();
        $view = new Kwf_Component_Renderer();

        $value = $view->renderMaster($root);
        $this->assertEquals('c3_rootmaster c1_box c3_root', $value);

        $value = $view->renderMaster($root->getChildComponent('_childpage'));
        $this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage', $value);

        $value = $view->renderComponent($root->getChildComponent('_childpage'));
        $this->assertEquals('c3_childpage', $value);

        $value = $view->renderMaster($root->getChildComponent('_childpage')->getChildComponent('_childpage'));
        $this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage2', $value);
    }

    public function testPlugin()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_Output_Plugin_Component');
        $root = Kwf_Component_Data_Root::getInstance();
        $view = new Kwf_Component_Renderer();

        $value = $view->renderMaster($root);
        // Eigentlicher Code zur Kontrolle in PluginAfter!
        $this->assertEquals('rootmaster pluginChild rootmasterend', $value);
    }

    public function testPartial()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_Output_Partial_Random_Component');
        $root = Kwf_Component_Data_Root::getInstance();
        $view = new Kwf_Component_Renderer();

        $value = $view->renderMaster($root);
        $this->assertTrue(in_array($value, array(
            'bar0bar1', 'bar0bar2', 'bar1bar2', 'bar1bar0', 'bar2bar0', 'bar2bar1'
        )));

        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_Output_Partial_Paging_Component');
        $value = $view->renderMaster(Kwf_Component_Data_Root::getInstance());
        $this->assertEquals('bar2', $value);
    }

    public function testDynamic()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_Output_Dynamic_Component');
        $output = new Kwf_Component_Renderer();

        $value = $output->renderMaster(Kwf_Component_Data_Root::getInstance());
        $this->assertEquals('dynamic |bar0-1|bar1-2|bar2-1', $value);
    }

    public function testComponentLink()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_Output_Link_Component');
        $output = new Kwf_Component_Renderer();
        $html = $output->renderComponent(Kwf_Component_Data_Root::getInstance());
        $this->assertEquals('<a href="/c1" rel="">C1</a> <a href="/foo?&amp;f1=1#a2" rel="bar" class="Bar">Foo</a>', $html);
    }

    public function testHasContent()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_Output_HasContent_Component');
        $output = new Kwf_Component_Renderer();

        $value = $output->renderMaster(Kwf_Component_Data_Root::getInstance());
        $this->assertEquals('root ', $value);
    }
}