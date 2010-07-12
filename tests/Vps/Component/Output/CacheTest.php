<?php
/**
 * @group Component_Output_Cache
 */
class Vps_Component_Output_CacheTest extends PHPUnit_Framework_TestCase
{
    private $_root;
    private $_renderer;
    private $_cacheModel;

    private function _setup($rootClass)
    {
        Vps_Component_Data_Root::setComponentClass($rootClass);
        $this->_root = Vps_Component_Data_Root::getInstance();

        $this->_renderer = new Vps_Component_Renderer();
        $this->_renderer->setEnableCache(true);

        $this->_cacheModel = new Vps_Component_Renderer_CacheModel(
            array('proxyModel' => new Vps_Model_FnF(array(
                'primaryKey' => 'fakeId',
                'columns' => array('fakeId', 'component_id', 'page_id', 'component_class', 'type', 'value', 'expire', 'deleted', 'content'),
                'uniqueColumns' => array('component_id', 'type', 'value')
            )))
        );
        $this->_renderer->setCacheModel($this->_cacheModel);
    }

    public function testC3()
    {
        $this->_setup('Vps_Component_Output_C3_Root_Component');
        $value = $this->_renderer->renderMaster($this->_root);
        $stats = $this->_renderer->getStats();
        $this->assertEquals('c3_rootmaster c1_box c3_root', $value);
        $this->assertEquals(3, $this->_cacheModel->countRows());
        $this->assertEquals(3, count($stats['rendered']));
        $this->assertEquals(3, count($stats['cacheSaved']));
        $this->assertEquals(0, count($stats['cachePreloaded']));
        $this->assertEquals(0, count($stats['cacheLoaded']));

        $value = $this->_renderer->renderMaster($this->_root);
        $stats = $this->_renderer->getStats();
        $this->assertEquals('c3_rootmaster c1_box c3_root', $value);
        $this->assertEquals(0, count($stats['rendered']));
        $this->assertEquals(0, count($stats['cacheSaved']));
        $this->assertEquals(3, count($stats['cachePreloaded']));
        $this->assertEquals(3, count($stats['cacheLoaded']));
    }

    public function testC3ChildPage()
    {
        $this->_setup('Vps_Component_Output_C3_Root_Component');
        $component = $this->_root->getChildComponent('_childpage')->getChildComponent('_childpage');
        $value = $this->_renderer->renderMaster($component);
        $stats = $this->_renderer->getStats();
        $this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage2', $value);
        $this->assertEquals(4, $this->_cacheModel->countRows());
        $this->assertEquals(4, count($stats['rendered']));
        $this->assertEquals(4, count($stats['cacheSaved']));
        $this->assertEquals(0, count($stats['cachePreloaded']));
        $this->assertEquals(0, count($stats['cacheLoaded']));

        $value = $this->_renderer->renderMaster($component);
        $stats = $this->_renderer->getStats();
        $this->assertEquals('c3_rootmaster c3_box c3_childpagemaster c3_childpage2', $value);
        $this->assertEquals(0, count($stats['rendered']));
        $this->assertEquals(0, count($stats['cacheSaved']));
        $this->assertEquals(3, count($stats['cachePreloaded']));
        $this->assertEquals(4, count($stats['cacheLoaded']));
    }

    public function testC2()
    {
        $this->_setup('Vps_Component_Output_C2_Root_Component');
        $value = $this->_renderer->renderMaster($this->_root);
        $this->assertEquals('c2_root c2_childmaster c2_child c2_childNoCache ', $value);
        $this->assertEquals(3, $this->_cacheModel->countRows());
        $stats = $this->_renderer->getStats();
        $this->assertEquals(4, count($stats['rendered']));
        $this->assertEquals(3, count($stats['cacheSaved']));
    }

    public function testC4()
    {
        $this->_setup('Vps_Component_Output_C4_Component');
        $value = $this->_renderer->renderMaster($this->_root);
        $this->assertEquals('c4', $value);
        $this->assertEquals(1, $this->_cacheModel->countRows());
        $row = $this->_cacheModel->getRows()->current();
        $this->assertTrue($row->expire > (time() + 9));
        $this->assertTrue($row->expire < (time() + 11));
        $stats = $this->_renderer->getStats();
        $this->assertEquals(1, count($stats['rendered']));
        $this->assertEquals(1, count($stats['cacheSaved']));
    }
}
