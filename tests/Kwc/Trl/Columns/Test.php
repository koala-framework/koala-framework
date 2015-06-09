<?php
class Kwc_Trl_Columns_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Columns_Root');
        Kwc_Columns_Component::createChildModel('Kwc_Trl_Columns_Columns_Component.Kwc_Trl_Columns_German')
            ->getProxyModel()->setData(array(
                array('id' => 1, 'component_id' => 'root-master_test', 'pos' => 1, 'visible' => 1),
                array('id' => 2, 'component_id' => 'root-master_test', 'pos' => 2, 'visible' => 0),
                array('id' => 3, 'component_id' => 'root-master_test', 'pos' => 3, 'visible' => 1)
            ));
    }

    public function testDeVisibilityClearCache()
    {
        $c = $this->_root->getComponentById('root-master_test');
        $html = $c->render(true, false);
        $this->assertContains('root-master_test-1', $html);
        $this->assertNotContains('root-master_test-2', $html);
        $this->assertContains('root-master_test-3', $html);

        $row = $this->_root->getComponentById('root-master_test-2', array('ignoreVisible' => true))->row;
        $row->visible = 1;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-master_test');
        $html = $c->render(true, false);
        $this->assertContains('root-master_test-1', $html);
        $this->assertContains('root-master_test-2', $html);
        $this->assertContains('root-master_test-3', $html);
    }

    public function testEnVisibilityClearCache()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render(true, false);
        $this->assertContains('root-en_test-1', $html);
        $this->assertNotContains('root-en_test-2', $html);
        $this->assertContains('root-en_test-3', $html);

        $row = $this->_root->getComponentById('root-master_test-2', array('ignoreVisible' => true))->row;
        $row->visible = 1;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render(true, false);
        $this->assertContains('root-en_test-1', $html);
        $this->assertContains('root-en_test-2', $html);
        $this->assertContains('root-en_test-3', $html);
    }

    public function testDeDeleteClearCache()
    {
        $c = $this->_root->getComponentById('root-master_test');
        $html = $c->render(true, false);
        $this->assertContains('root-master_test-1', $html);
        $this->assertNotContains('root-master_test-2', $html);
        $this->assertContains('root-master_test-3', $html);

        $row = $this->_root->getComponentById('root-master_test-3', array('ignoreVisible' => true))->row;
        $row->delete();
        $this->_process();

        $c = $this->_root->getComponentById('root-master_test');
        $html = $c->render(true, false);
        $this->assertContains('root-master_test-1', $html);
        $this->assertNotContains('root-master_test-2', $html);
        $this->assertNotContains('root-master_test-3', $html);
    }

    public function testEnDeleteClearCache()
    {
        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render(true, false);
        $this->assertContains('root-en_test-1', $html);
        $this->assertNotContains('root-en_test-2', $html);
        $this->assertContains('root-en_test-3', $html);

        $row = $this->_root->getComponentById('root-master_test-3', array('ignoreVisible' => true))->row;
        $row->delete();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_test');
        $html = $c->render(true, false);
        $this->assertContains('root-en_test-1', $html);
        $this->assertNotContains('root-en_test-2', $html);
        $this->assertNotContains('root-en_test-3', $html);
    }
}
