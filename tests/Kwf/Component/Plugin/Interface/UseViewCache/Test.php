<?php
class Kwf_Component_Plugin_Interface_UseViewCache_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num = 0;
        parent::setUp('Kwf_Component_Plugin_Interface_UseViewCache_Root');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testPlugin()
    {
        $c = $this->_root->getComponentByClass('Kwf_Component_Plugin_Interface_UseViewCache_Component');
        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = true;
        $html1 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html2 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html3 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;

        $this->assertEquals($html1, $html2);
        $this->assertEquals($html1, $html3);

        apc_clear_cache('user');
        Kwf_Cache::factory('Core', 'Memcached', array(
            'lifetime'=>null,
            'automatic_cleaning_factor' => false,
            'automatic_serialization'=>true))->clean();
        Kwf_Cache_Simple::resetZendCache();

        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = false;
        $html4 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html5 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html6 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $this->assertNotEquals($html3, $html4);
        $this->assertNotEquals($html4, $html5);
        $this->assertNotEquals($html4, $html6);
        $this->assertNotEquals($html5, $html6);

        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = true;
        $html7 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html8 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $this->assertEquals($html7, $html3);
        $this->assertEquals($html8, $html3);
    }

    public function testPluginWithMaster()
    {
        $c = $this->_root->getComponentByClass('Kwf_Component_Plugin_Interface_UseViewCache_Component');
        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = true;
        $html1 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html2 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html3 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;

        $this->assertEquals($html1, $html2);
        $this->assertEquals($html1, $html3);

        apc_clear_cache('user');
        Kwf_Cache::factory('Core', 'Memcached', array(
            'lifetime'=>null,
            'automatic_cleaning_factor' => false,
            'automatic_serialization'=>true))->clean();
        Kwf_Cache_Simple::resetZendCache();

        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = false;
        $html4 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html5 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html6 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $this->assertNotEquals($html3, $html4);
        $this->assertNotEquals($html4, $html5);
        $this->assertNotEquals($html4, $html6);
        $this->assertNotEquals($html5, $html6);

        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = true;
        $html7 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html8 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $this->assertEquals($html7, $html3);
        $this->assertEquals($html8, $html3);
    }

    public function testEmptyCacheDontCacheIfNoViewCache()
    {
        $c = $this->_root->getComponentByClass('Kwf_Component_Plugin_Interface_UseViewCache_Component');

        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = false;
        $html1 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html2 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html3 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $this->assertNotEquals($html1, $html2);
        $this->assertNotEquals($html1, $html3);
        $this->assertNotEquals($html2, $html3);

        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = true;
        $html4 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html5 = $c->render();
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $this->assertNotEquals($html4, $html1);
        $this->assertNotEquals($html4, $html2);
        $this->assertNotEquals($html4, $html3);
        $this->assertEquals($html4, $html5);
    }

    public function testEmptyCacheDontCacheIfNoViewCacheMaster()
    {
        $c = $this->_root->getComponentByClass('Kwf_Component_Plugin_Interface_UseViewCache_Component');
        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = false;
        $html1 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html2 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html3 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $this->assertNotEquals($html1, $html2);
        $this->assertNotEquals($html1, $html3);
        $this->assertNotEquals($html2, $html3);

        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = true;
        $html4 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $html5 = $c->render(true, true);
        Kwf_Component_Plugin_Interface_UseViewCache_Component::$num++;
        $this->assertNotEquals($html4, $html1);
        $this->assertNotEquals($html4, $html2);
        $this->assertNotEquals($html4, $html3);
        $this->assertEquals($html4, $html5);
    }
}
