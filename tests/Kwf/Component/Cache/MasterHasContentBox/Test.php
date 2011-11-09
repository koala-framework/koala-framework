<?php
/**
 * @group 
 */
class Kwf_Component_Cache_MasterHasContentBox_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_MasterHasContentBox_Root_Component');
        /*
        root
          -box1
          -box2
          _test1
            -box1
            -box2
          _test2
            -box1
            -box2
         */
    }

    public function testContent()
    {
        $c = $this->_root->getComponentById('root_page1');
        $html = $c->render(true, true);
        $this->assertContains('box1', $html);
        $this->assertContains('asdf', $html); //box1 content
        $this->assertNotContains('box2', $html);
    }

    public function testRemovedBoxContent()
    {
        $c = $this->_root->getComponentById('root_page1');
        $c->render(true, true);

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MasterHasContentBox_Box_TestModel')
            ->getRow('root_page1-box1');
        $row->content = '';
        $row->save();

        $this->_process();

        $html = $c->render(true, true);
        $this->assertNotContains('box1', $html);
        $this->assertNotContains('asdf', $html); //box1 content
    }

    public function testAddedBoxContent()
    {
        $c = $this->_root->getComponentById('root_page2');
        $c->render(true, true);

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MasterHasContentBox_Box_TestModel')
            ->createRow();
        $row->component_id = 'root_page2-box1';
        $row->content = 'xxy';
        $row->save();

        $this->_process();

        $html = $c->render(true, true);
        $this->assertContains('box1', $html);
        $this->assertContains('xxy', $html); //box1 content
    }

    public function testAddedUniqueBoxContent()
    {
        $c = $this->_root->getComponentById('root_page1');
        $c->render(true, true);

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MasterHasContentBox_Box_TestModel')
            ->createRow();
        $row->component_id = 'root-box2';
        $row->content = 'xxyz';
        $row->save();

        $this->_process();

        $html = $c->render(true, true);
        $this->markTestIncomplete();
        $this->assertContains('box2', $html);
        $this->assertContains('xxyz', $html); //box2 content
    }

    public function testRemovedUniqueBoxContent()
    {
        $c = $this->_root->getComponentById('root_page1');
        $c->render(true, true);

        //first add content - so we can remove again later
        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MasterHasContentBox_Box_TestModel')
            ->createRow();
        $row->component_id = 'root-box2';
        $row->content = 'xxyz';
        $row->save();

        $this->_process();

        $c->render(true, true);

        $row->content = '';
        $row->save();

        $this->_process();

        $html = $c->render(true, true);
        $this->markTestIncomplete();
        $this->assertNotContains('box2', $html);
    }
}
