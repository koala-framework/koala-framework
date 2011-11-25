<?php
/**
 * @group Generator_IsVisible
 */
class Kwf_Component_Generator_IsVisible_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_IsVisible_Root');
    }

    public function testTable()
    {
        $this->assertTrue($this->_isVisible('root-1'));
        $this->assertTrue($this->_isVisible('root-1-child'));
        $this->assertFalse($this->_isVisible('root-2'));
        $this->assertFalse($this->_isVisible('root-2-child'));
    }

    public function testPages()
    {
        $this->assertFalse($this->_isVisible('1'));
        $this->assertFalse($this->_isVisible('1-child'));
        $this->assertFalse($this->_isVisible('2'));
        $this->assertFalse($this->_isVisible('2-child'));
        $this->assertTrue($this->_isVisible('3'));
        $this->assertTrue($this->_isVisible('3-child'));
    }

    private function _isVisible($componentId)
    {
        return $this->_root
            ->getComponentById($componentId, array('ignoreVisible' => true))
            ->isVisible();
    }
}
