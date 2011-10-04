<?php
/**
 * @group Component_Output_Cache
 * @group Component_Output_CacheSlow
 * @group slow
 */
class Vps_Component_Output_CacheSlowTest extends Vps_Test_TestCase
{
    private $_root;
    private $_renderer;

    private function _setup($rootClass)
    {
        Vps_Component_Data_Root::setComponentClass($rootClass);
        $this->_root = Vps_Component_Data_Root::getInstance();

        $this->_renderer = new Vps_Component_Renderer();
        $this->_renderer->setEnableCache(true);
        Vps_Component_Cache::setInstance(Vps_Component_Cache::CACHE_BACKEND_FNF);
        apc_clear_cache('user');
    }

    /*
     * Funktioniert nicht, weil TTL innerhalb eines Requests nicht berücksichtigt wird
     */
    public function testApcCli()
    {
        apc_store('foo', 'bar', 1);
        $this->assertEquals(apc_fetch('foo'), 'bar');
        //sleep(2);
        //$this->assertNull(apc_fetch('foo'));
    }

    public function testC4()
    {
        $this->_setup('Vps_Component_Output_C4_Component');
        $this->_root->render();

        $model = Vps_Component_Cache::getInstance()->getModel('cache');
        $row = $model->getRows()->current();
        $content = $row->content;
        $this->_root->render();
        $this->assertEquals($content, $row->content);
        // muss hier gemacht werden, weil TTL nicht funktioniert
        apc_delete(Vps_Cache::getUniquePrefix() . '-cc-root/component/');
        sleep(3);
        $this->_root->render();
        $this->assertNotEquals($content, $row->content);
    }
}
