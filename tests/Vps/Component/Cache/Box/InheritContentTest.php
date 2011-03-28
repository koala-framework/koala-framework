<?php
/**
 * @group Component_Cache
 * @group Component_Cache_Box
 * @group Component_Cache_Box_InheritContent
 */
class Vps_Component_Cache_Box_InheritContentTest extends Vpc_TestAbstract
{
    public function setUp()
    {
        /*
        root
        |-ic
        | |-child
        |-1
        | |-ic
        |   |-child
        |-2
          |-ic
            |-child
        */
        parent::setUp('Vps_Component_Cache_Box_IcRoot_Component');
    }

    function testInheritContent()
    {
        $root = $this->_root;
        $child = $root->getComponentById('1');
        $childchild = $root->getComponentById('2');
        $model = Vps_Model_Abstract::getInstance('Vps_Component_Cache_Box_IcRoot_InheritContent_Child_Model');

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
        $model = Vps_Model_Abstract::getInstance('Vps_Component_Cache_Box_IcRoot_InheritContent_Child_Model');

        $this->assertEquals('1-ic-child', $c1->render(true, true));
        $this->assertEquals('1-ic-child', $c2->render(true, true));
        $model->getRow('1-ic-child')->delete();
        $this->_process();
        $this->assertEquals('root-ic-child', $c1->render(true, true));
        $this->assertEquals('root-ic-child', $c2->render(true, true));
    }
}
