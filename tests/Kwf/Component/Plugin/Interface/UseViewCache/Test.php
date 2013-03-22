<?php
class Kwf_Component_Plugin_Interface_UseViewCache_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
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
        $html2 = $c->render();
        $html3 = $c->render();

        $this->assertEquals($html1, $html2);
        $this->assertEquals($html1, $html3);

        apc_clear_cache('user');
        Kwf_Cache::factory('Core', 'Memcached', array(
            'lifetime'=>null,
            'automatic_cleaning_factor' => false,
            'automatic_serialization'=>true))->clean();

        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = false;
        $html4 = $c->render();
        $html5 = $c->render();
        $html6 = $c->render();
        $this->assertNotEquals($html3, $html4);
        $this->assertNotEquals($html4, $html5);
        $this->assertNotEquals($html4, $html6);
        $this->assertNotEquals($html5, $html6);

        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = true;
        $html7 = $c->render();
        $html8 = $c->render();
        $this->assertEquals($html7, $html3);
        $this->assertEquals($html8, $html3);
    }
}
