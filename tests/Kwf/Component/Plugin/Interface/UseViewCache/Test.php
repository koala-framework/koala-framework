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

        Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache = false;
        $html1 = $c->render();
        $html2 = $c->render();
        $html3 = $c->render();
        $this->assertNotEquals($html1, $html2);
        $this->assertNotEquals($html1, $html3);
        $this->assertNotEquals($html2, $html3);
    }
}
