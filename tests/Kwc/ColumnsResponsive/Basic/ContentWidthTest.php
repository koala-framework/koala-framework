<?php
class Kwc_ColumnsResponsive_Basic_ContentWidthTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_ColumnsResponsive_Basic_Root');
    }

    public function test2ColumnsWith50_50()
    {
        $c = $this->_root->getComponentById('3000-1-1');
        $this->assertEquals(450, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-1-2');
        $this->assertEquals(450, (int)$c->getComponent()->getContentWidth());
    }

    public function test2ColumnsWith75_25()
    {
        $c = $this->_root->getComponentById('3000-2-1');
        $this->assertEquals(675, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-2-2');
        $this->assertEquals(225, (int)$c->getComponent()->getContentWidth());
    }

    public function test3ColumnsWith33_33_33()
    {
        $c = $this->_root->getComponentById('3000-3-1');
        $this->assertEquals(300, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-3-2');
        $this->assertEquals(300, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-3-3');
        $this->assertEquals(300, (int)$c->getComponent()->getContentWidth());
    }

    public function test3ColumnsWith25_50_25()
    {
        $c = $this->_root->getComponentById('3000-4-1');
        $this->assertEquals(225, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-4-2');
        $this->assertEquals(450, (int)$c->getComponent()->getContentWidth());

        $c = $this->_root->getComponentById('3000-4-3');
        $this->assertEquals(225, (int)$c->getComponent()->getContentWidth());
    }
}
