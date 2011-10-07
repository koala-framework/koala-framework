<?php
/**
 * @group Component_Output_Cache
 * @group Component_Output_CacheSlow
 * @group slow
 */
class Kwf_Component_Output_CacheSlowTest extends Kwf_Test_TestCase
{
    private $_root;

    private function _setup($rootClass)
    {
        Kwf_Component_Data_Root::setComponentClass($rootClass);
        $this->_root = Kwf_Component_Data_Root::getInstance();

        Kwf_Component_Cache::setInstance(Kwf_Component_Cache::CACHE_BACKEND_FNF);
        apc_clear_cache('user');

        Kwf_Registry::get('config')->debug->componentCache->disable = false;
        Kwf_Config::deleteValueCache('debug.componentCache.disable');
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
        $this->_setup('Kwf_Component_Output_C4_Component');
        $this->_root->render();

        $model = Kwf_Component_Cache::getInstance()->getModel('cache');
        $row = $model->getRows()->current();
        $content = $row->content;
        $this->_root->render();
        $this->assertEquals($content, $row->content);
        // muss hier gemacht werden, weil TTL nicht funktioniert
        apc_delete(Kwf_Cache::getUniquePrefix() . '-cc-root/component/');
        sleep(3);
        $this->_root->render();
        $this->assertNotEquals($content, $row->content);
    }
}
