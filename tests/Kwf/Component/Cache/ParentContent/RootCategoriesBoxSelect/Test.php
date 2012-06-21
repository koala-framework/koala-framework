<?php
/**
 * @group 
 */
class Kwf_Component_Cache_ParentContent_RootCategoriesBoxSelect_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_ParentContent_RootCategoriesBoxSelect_Component');
    }

    private function _assertRenderedContents($exprectedHtml, $componentId)
    {
        $c = $this->_root->getComponentById($componentId);
        if (!$c) throw new Kwf_Exception("didn't find component with id '$componentId'");
        $this->assertEquals($exprectedHtml, $c->render(true, true));
    }

    public function testInitialContent()
    {
        $c = $this->_root->getComponentById('1-box');
        $this->assertEquals('Kwf_Component_Cache_ParentContent_RootCategoriesBoxSelect_Box_Component', $c->componentClass);

        $this->_assertRenderedContents('foo', '1');
        $this->_assertRenderedContents('foo', '2');
        $this->_assertRenderedContents('', '3');
    }

    public function testChangeRoot()
    {
        //fill cache
        $this->_assertRenderedContents('', '3');

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_ParentContent_RootCategoriesBoxSelect_BoxSelectModel');
        $r = $m->createRow();
        $r->component_id = 'root-box';
        $r->component = 'box';
        $r->save();

        $this->_process();

        $this->_assertRenderedContents('foo', '3');

        //and now test with update (above was insert)
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_ParentContent_RootCategoriesBoxSelect_BoxSelectModel');
        $r = $m->getRow('root-box');
        $r->component = 'parentContent';
        $r->save();

        $this->_process();

        $this->_assertRenderedContents('', '3');
    }
}
