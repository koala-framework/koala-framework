<?php
/**
 * @group Vpc_Basic_LinkTagIntern
 **/
class Vpc_Basic_LinkTagIntern_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_LinkTagIntern_Root');
    }

    public function testDependsOnRow()
    {
        $delRow = $this->_root->getGenerator('page')->getModel()->getRow(1310);

        $a = Vpc_Admin::getInstance('Vpc_Basic_LinkTagIntern_TestComponent');
        $depends = $a->getComponentsDependingOnRow($delRow);

        $this->assertEquals(1, count($depends));

        $depend = current($depends);
        $this->assertEquals($this->_root->getComponentById(1300)->componentId, $depend->componentId);
    }

    public function testUrlAndRel()
    {
        $c = $this->_root->getComponentById(1300);
        $this->assertEquals('/bar', $c->url);
        $this->assertEquals('', $c->rel);
        $this->assertTrue($c->getComponent()->hasContent());
    }
    public function testHtml()
    {
        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($this->_root->getComponentById(1300));
        $this->assertEquals('<a href="/bar">', $html);
    }

    public function testEmpty()
    {
        //ist das das gewünscht verhalten?
        $c = $this->_root->getComponentById(1301);
        $this->assertEquals('', $c->url);
        $this->assertEquals('', $c->rel);
        $this->assertFalse($c->getComponent()->hasContent());
    }

    public function testLinkToNotExistingSite()
    {
        //ist das das gewünscht verhalten?
        $c = $this->_root->getComponentById(1302);
        $this->assertEquals('', $c->url);
        $this->assertEquals('', $c->rel);
        $this->assertFalse($c->getComponent()->hasContent());
    }

    public function testLinkToInvisibleSite()
    {
        //ist das das gewünscht verhalten?
        $c = $this->_root->getComponentById(1303);
        $this->assertEquals('', $c->url);
        $this->assertEquals('', $c->rel);
        $this->assertFalse($c->getComponent()->hasContent());
    }
}
