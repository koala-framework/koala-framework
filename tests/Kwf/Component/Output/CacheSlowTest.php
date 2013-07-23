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
        Kwf_Cache::factory('Core', 'Memcached', array(
            'lifetime'=>null,
            'automatic_cleaning_factor' => false,
            'automatic_serialization'=>true))->clean();
        Kwf_Cache_Simple::resetZendCache();


        Kwf_Registry::get('config')->debug->componentCache->disable = false;
        Kwf_Config::deleteValueCache('debug.componentCache.disable');
    }

    public function testApcCli()
    {
        Kwf_Cache_Simple::add('foo', 'bar', 1);
        $this->assertEquals(Kwf_Cache_Simple::fetch('foo'), 'bar');
        sleep(2);
        $this->assertFalse(Kwf_Cache_Simple::fetch('foo'));
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
        sleep(3);
        $this->_root->render();
        $this->assertNotEquals($content, $row->content);
    }

    public function testC4FullPage()
    {
        $this->_setup('Kwf_Component_Output_C4_Component');

        //sleep until right after the start of a new second
        //to avoid race conditions in test
        $mt = explode(" ", microtime());
        usleep((1-$mt[0])*1000*1000);

        $t = time();

        //render first time
        $html = $this->_root->render(true, true);
        $this->assertContains('c4 '.$t, $html);

        //render again - still same cache content
        $html = $this->_root->render(true, true);
        $this->assertContains('c4 '.$t, $html);

        //render after 1sec - still same cache content
        sleep(1);
        $html = $this->_root->render(true, true);
        $this->assertContains('c4 '.$t, $html);

        //render after 2 more sec, cache must be expired now
        sleep(2);
        $t = time();
        $html = $this->_root->render(true, true);
        $this->assertContains('c4 '.$t, $html);
    }
}
