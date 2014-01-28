<?php
class Kwc_Columns_Basic_ContentWidthTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Columns_Basic_Root');
    }

    public function test2ColumnsWith50_50()
    {
        $c = $this->_root->getComponentById('3000-1-1');
        $this->assertEquals(480, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-1-2');
        $this->assertEquals(480, (int)$c->getComponent()->getContentWidth());
    }

    public function test2ColumnsWith75_25()
    {
        $c = $this->_root->getComponentById('3000-2-3');
        $this->assertEquals(675, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-2-4');
        $this->assertEquals(480, (int)$c->getComponent()->getContentWidth());
    }

    public function test3ColumnsWith33_33_33()
    {
        $c = $this->_root->getComponentById('3000-3-5');
        $this->assertEquals(480, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-3-6');
        $this->assertEquals(480, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-3-7');
        $this->assertEquals(480, (int)$c->getComponent()->getContentWidth());
    }

    public function test3ColumnsWith25_50_25()
    {
        $c = $this->_root->getComponentById('3000-4-8');
        $this->assertEquals(480, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-4-9');
        $this->assertEquals(480, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-4-10');
        $this->assertEquals(480, (int)$c->getComponent()->getContentWidth());
    }
}
