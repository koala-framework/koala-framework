<?php
/**
 * @group Component_Output_Cache
 */
class Kwf_Component_Output_CacheTest extends Kwf_Test_TestCase
{
    private $_root;
    private $_renderer;

    private function _setup($rootClass)
    {
        Kwf_Component_Data_Root::setComponentClass($rootClass);
        $this->_root = Kwf_Component_Data_Root::getInstance();

        $this->_renderer = new Kwf_Component_Renderer();
        $this->_renderer->setEnableCache(true);
        Kwf_Component_Cache::setInstance(Kwf_Component_Cache::CACHE_BACKEND_FNF);
        apc_clear_cache('user');
        Kwf_Cache::factory('Core', 'Memcached', array(
            'lifetime'=>null,
            'automatic_cleaning_factor' => false,
            'automatic_serialization'=>true))->clean();
        Kwf_Cache_Simple::resetZendCache();
    }

    public function testC3()
    {
        $this->_setup('Kwf_Component_Output_C3_Root_Component');
        $model = Kwf_Component_Cache::getInstance()->getModel('cache');
        $value = $this->_renderer->renderMaster($this->_root);
        $this->assertRegExp('#c3_rootmaster c1_box .*c3_root.*#s', $value);

        //page, master, 2 component and 1 fullPage
        $this->assertEquals(5, $model->countRows());

        $value = $this->_renderer->renderMaster($this->_root);
        $this->assertRegExp('#c3_rootmaster c1_box .*c3_root.*#s', $value);
    }

    public function testC3ChildPage()
    {
        $this->_setup('Kwf_Component_Output_C3_Root_Component');
        $model = Kwf_Component_Cache::getInstance()->getModel('cache');
        $component = $this->_root->getChildComponent('_childpage')->getChildComponent('_childpage');
        $value = $this->_renderer->renderMaster($component);
        $this->assertRegExp('#c3_rootmaster c3_box c3_childpagemaster .*c3_childpage2.*#s', $value);
        $this->assertEquals(5, $model->countRows());

        $value = $this->_renderer->renderMaster($component);
        $this->assertRegExp('#c3_rootmaster c3_box c3_childpagemaster .*c3_childpage2.*#s', $value);
    }

    public function testC2()
    {
        $this->_setup('Kwf_Component_Output_C2_Root_Component');
        $model = Kwf_Component_Cache::getInstance()->getModel('cache');
        $value = $this->_renderer->renderMaster($this->_root);
        $this->assertRegExp('#c2_root c2_child c2_childNoCache #s', $value);

        //page, master, 2 component, 1 fullPage
        $this->assertEquals(5, $model->countRows());
    }

    public function testC4()
    {
        $this->_setup('Kwf_Component_Output_C4_Component');
        $model = Kwf_Component_Cache::getInstance()->getModel('cache');
        $value = $this->_renderer->renderMaster($this->_root);
        $this->assertRegExp('#c4 #s', $value);

        //page, master, 1 component, 1 fullPage
        $this->assertEquals(4, $model->countRows());

        $s = new Kwf_Model_Select();
        $s->whereEquals('type', 'component');
        $row = $model->getRows($s)->current();
        $this->assertTrue($row->expire > (time() + 1));
        $this->assertTrue($row->expire < (time() + 5));

        $s = new Kwf_Model_Select();
        $s->whereEquals('type', 'fullPage');
        $row = $model->getRows($s)->current();
        $this->assertTrue($row->expire > (time() + 1));
        $this->assertTrue($row->expire < (time() + 5));
    }
}
