<?php
/**
 * @group Component_Cache
 * @group Component_Cache_Box
 * @group Component_Cache_Box_InheritContent
 */
class Kwf_Component_Cache_Box_InheritContentTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        /*
        root
        |-ic
        | |-child
        |-1
          |-ic
          | |-child
          |-2
            |-ic
              |-child
        */
        parent::setUp('Kwf_Component_Cache_Box_IcRoot_Component');
    }

    function testInheritContent()
    {
        $root = $this->_root;
        $child = $root->getComponentById('1');
        $childchild = $root->getComponentById('2');
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Box_IcRoot_InheritContent_Child_Model');

        $render = $root->render(true, true);
        $this->assertEquals('root-ic-child', $render);
        $render = $child->render(true, true);
        $this->assertEquals('1-ic-child', $render);
        $render = $childchild->render(true, true);
        $this->assertEquals('1-ic-child', $render);

        $row = $model->getRow('1-ic-child');
        $row->content = 'foo';
        $row->save();
        $this->_process();
        $render = $child->render(true, true);
        $this->assertEquals('foo', $render);
        $render = $childchild->render(true, true);
        $this->assertEquals('foo', $render);
    }

    function testDelete()
    {
        $c1 = $this->_root->getComponentById('1');
        $c2 = $this->_root->getComponentById('2');
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Box_IcRoot_InheritContent_Child_Model');

        $this->assertEquals('1-ic-child', $c1->render(true, true));
        $this->assertEquals('1-ic-child', $c2->render(true, true));
        $row = $model->getRow('1-ic-child');
        $row->has_content = null;
        $row->save();
        $this->_process();
        $this->assertEquals('root-ic-child', $c1->render(false, true));
        $this->assertEquals('root-ic-child', $c2->render(false, true));
        $this->assertEquals('root-ic-child', $c1->render(true, true));
        $this->assertEquals('root-ic-child', $c2->render(true, true));
    }

    function testHasContent()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Box_IcRoot_InheritContent_Child_Model');

        $events = Kwf_Component_Cache_Box_IcRoot_Events::getInstance(
            'Kwf_Component_Cache_Box_IcRoot_Events',
            array('componentClass' => 'Kwf_Component_Cache_Box_IcRoot_Component')
        );
        $count = 0;

        $row = $model->getRow('1-ic-child');
        $row->content = 'foo';
        $row->save();
        $this->_process();
        $this->assertEquals($count, $events->countCalled);

        $row = $model->getRow('1-ic-child');
        $row->has_content = false;
        $row->save();
        $this->_process();
        $this->assertEquals($count, $events->countCalled);

        $row = $model->getRow('root-ic-child');
        $row->has_content = false;
        $row->save();
        $this->_process();
        $this->assertEquals(++$count, $events->countCalled);

        $row = $model->getRow('2-ic-child');
        $row->has_content = true;
        $row->save();
        $this->_process();
        $this->assertEquals(++$count, $events->countCalled);
        
        $row = $model->getRow('2-ic-child');
        $row->has_content = false;
        $row->save();
        $this->_process();
        $this->assertEquals(++$count, $events->countCalled);
        
        $row = $model->getRow('root-ic-child');
        $row->has_content = true;
        $row->save();
        $this->_process();
        $this->assertEquals(++$count, $events->countCalled);

        $row = $model->getRow('1-ic-child');
        $row->has_content = true;
        $row->save();
        $this->_process();
        $this->assertEquals($count, $events->countCalled);
    }
}
