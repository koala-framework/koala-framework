<?php
/**
 * @group 
 */
class Kwf_Component_Cache_ProcessInput_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_ProcessInput_Root');
    }

    public function testEmpty()
    {
        $c = $this->_root->getcomponentById('1');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(0, count($pi));
    }

    public function testDirectProcessInput()
    {
        $c = $this->_root->getcomponentById('2');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(1, count($pi));
        $pi = array_values($pi);
        $this->assertEquals('2', $pi[0]->componentId);
    }

    public function testContainsProcessInput()
    {
        $c = $this->_root->getcomponentById('3');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(1, count($pi));
        $pi = array_values($pi);
        $this->assertEquals('3-withProcessInput', $pi[0]->componentId);
    }

    public function testParagraphsDirectProcessInput()
    {
        $c = $this->_root->getcomponentById('4');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(2, count($pi));
        $pi = array_values($pi);
        $this->assertEquals('4-1', $pi[0]->componentId);
        $this->assertEquals('4-2-withProcessInput', $pi[1]->componentId);
    }

    public function testAddDirectProcessInput()
    {
        $c = $this->_root->getComponentById('5');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(0, count($pi));

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_ProcessInput_Paragraphs_TestModel');
        $row = $m->createRow();
        $row->component_id = '5';
        $row->visible = true;
        $row->pos = 1;
        $row->component = 'withProcessInput';
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('5');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(1, count($pi));
        $pi = array_values($pi);
        $this->assertEquals('5-3', $pi[0]->componentId);
    }

    public function testAddContainsProcessInput()
    {
        $c = $this->_root->getcomponentById('5');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(0, count($pi));

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_ProcessInput_Paragraphs_TestModel');
        $row = $m->createRow();
        $row->component_id = '5';
        $row->visible = true;
        $row->pos = 1;
        $row->component = 'containsWithProcessInput';
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('5');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(1, count($pi));
        $pi = array_values($pi);
        $this->assertEquals('5-3-withProcessInput', $pi[0]->componentId);
    }

    public function testRemoveDirectProcessInput()
    {
        $c = $this->_root->getComponentById('4');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(2, count($pi));

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_ProcessInput_Paragraphs_TestModel');
        $row = $m->getRow(1);
        $row->visible = false;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('4');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(1, count($pi));
        $pi = array_values($pi);
        $this->assertEquals('4-2-withProcessInput', $pi[0]->componentId);
    }

    public function testRemoveContainsProcessInput()
    {
        $c = $this->_root->getComponentById('4');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(2, count($pi));

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_ProcessInput_Paragraphs_TestModel');
        $row = $m->getRow(2);
        $row->delete();
        $this->_process();

        $c = $this->_root->getComponentById('4');
        $pi = Kwf_Component_Abstract_ContentSender_Default::getProcessInputComponents($c);
        $this->assertEquals(1, count($pi));
        $pi = array_values($pi);
        $this->assertEquals('4-1', $pi[0]->componentId);
    }
}
