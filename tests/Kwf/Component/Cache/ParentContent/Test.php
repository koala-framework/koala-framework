<?php
/**
 * @group Component_Cache_ParentContent
 */
class Kwf_Component_Cache_ParentContent_Test extends Kwc_TestAbstract
{
    public function testAlternative()
    {
        $root = $this->_init('Kwf_Component_Cache_ParentContent_RootAlternative_Component');
        $p1 = $root->getComponentById('1');
        $p2 = $root->getComponentById('2');
        
        $this->assertEquals('foo', $p1->render(true, true));
        $this->assertEquals('foo', $p2->render(true, true));
        
        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_ParentContent_RootAlternative_Box_Model')
            ->getRow('1-box');
        $row->has_content = false;
        $row->save();
        $this->_process();
        
        $this->assertEquals('', $p1->render(true, true));
        $this->assertEquals('', $p2->render(true, true));
    }

    public function testBoxSelect()
    {
        $root = $this->_init('Kwf_Component_Cache_ParentContent_RootBoxSelect_Component');
        $p1 = $root->getComponentById('1');
        $p2 = $root->getComponentById('2');
        
        $this->assertEquals('foo', $p1->render(true, true));
        $this->assertEquals('foo', $p2->render(true, true));
        
        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_ParentContent_RootBoxSelect_Box_Model')
            ->getRow('1-box');
        $row->has_content = false;
        $row->save();
        $this->_process();
        
        $this->assertEquals('', $p1->render(true, true));
        $this->assertEquals('', $p2->render(true, true));
    }
}
