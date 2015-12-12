<?php
/**
 * @group Component_Output
 */
class Kwf_Component_Output_Test extends Kwc_TestAbstract
{
    public function testMaster()
    {
        $this->_init('Kwf_Component_Output_C3_Root_Component');
        $root = Kwf_Component_Data_Root::getInstance();
        $view = new Kwf_Component_Renderer();

        //$value = $view->renderMaster($root);
        //$this->assertEquals('c3_rootmaster c1_box c3_root', $value);

        $value = $view->renderMaster($root->getChildComponent('_childpage'));
        $this->assertRegExp('#c3_rootmaster c3_box c3_childpagemaster .*c3_childpage.*#s', $value);

        //$value = $view->renderMaster($root->getChildComponent('_childpage')->getChildComponent('_childpage'));
        //$this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage2', $value);
    }

    public function testC1()
    {
        $this->_init('Kwf_Component_Output_C1_Root_Component');

        $root = Kwf_Component_Data_Root::getInstance();
        $view = new Kwf_Component_Renderer();

        $value = $view->renderComponent($root->getChildComponent('-child'));
        $this->assertEquals('plugin(plugin(c1_child c1_childchild))', $value);

        //re-render, now cached
        $value = $view->renderComponent($root->getChildComponent('-child'));
        $this->assertEquals('plugin(plugin(c1_child c1_childchild))', $value);

        $value = $view->renderMaster($root);
        $this->assertRegExp('#c1_rootmaster c1_box .*c1_root plugin\(plugin\(c1_child c1_childchild\)\).*#s', $value);

        $value = $view->renderComponent($root);
        $this->assertEquals('c1_root plugin(plugin(c1_child c1_childchild))', $value);
    }

    public function testC3()
    {
        $this->_init('Kwf_Component_Output_C3_Root_Component');

        $root = Kwf_Component_Data_Root::getInstance();
        $view = new Kwf_Component_Renderer();

        $value = $view->renderMaster($root);
        $this->assertRegExp('#c3_rootmaster c1_box .*c3_root.*#s', $value);

        $value = $view->renderMaster($root->getChildComponent('_childpage'));
        $this->assertRegExp('#c3_rootmaster c3_box c3_childpagemaster .*c3_childpage.*#s', $value);

        $value = $view->renderComponent($root->getChildComponent('_childpage'));
        $this->assertEquals('c3_childpage', $value);

        $value = $view->renderMaster($root->getChildComponent('_childpage')->getChildComponent('_childpage'));
        $this->assertRegExp('#c3_rootmaster c3_box c3_childpagemaster .*c3_childpage2.*#s', $value);
    }

    public function testPlugin()
    {
        $this->_init('Kwf_Component_Output_Plugin_Component');

        $root = Kwf_Component_Data_Root::getInstance();
        $view = new Kwf_Component_Renderer();

        $value = $view->renderMaster($root);
        // Eigentlicher Code zur Kontrolle in PluginAfter!
        $this->assertRegExp('#rootmaster .*pluginChild.* rootmasterend#s', $value);
    }

    public function testPartialRandom()
    {
        $this->_init('Kwf_Component_Output_Partial_Random_Component');

        $root = Kwf_Component_Data_Root::getInstance();
        $view = new Kwf_Component_Renderer();

        $value = $view->renderMaster($root);
        $this->assertRegExp('#bar[012]bar[012]#', $value);
    }

    public function testPartialPaging()
    {
        $this->_init('Kwf_Component_Output_Partial_Paging_Component');
        $view = new Kwf_Component_Renderer();
        $value = $view->renderMaster(Kwf_Component_Data_Root::getInstance());
        $this->assertRegExp('#bar2#', $value);
    }

    public function testDynamic()
    {
        $this->_init('Kwf_Component_Output_Dynamic_Component');

        $output = new Kwf_Component_Renderer();

        $value = $output->renderMaster(Kwf_Component_Data_Root::getInstance());
        $this->assertRegExp('#dynamic |bar0-1|bar1-2|bar2-1#', $value);
    }

    public function testComponentLink()
    {
        $this->_init('Kwf_Component_Output_Link_Component');
        $this->_root->setFilename('');

        $output = new Kwf_Component_Renderer();
        $html = $output->renderComponent(Kwf_Component_Data_Root::getInstance());
        $this->assertEquals('<a href="/c1">C1</a> <a href="/foo?f1=1#a2" class="Bar" rel="bar">Foo</a>', $html);
    }

    public function testHasContent()
    {
        $this->_init('Kwf_Component_Output_HasContent_Component');

        $output = new Kwf_Component_Renderer();

        $value = $output->renderMaster(Kwf_Component_Data_Root::getInstance());
        $this->assertRegExp('#root #', $value);
    }
}
