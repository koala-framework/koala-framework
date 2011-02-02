<?php
/**
 * @group Component_Cache
 * @group Component_Cache_Box
 */
class Vps_Component_Cache_Box_InheritContentTest extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_Box_IcRoot_Component');
    }

    function testInheritContent()
    {
        // TODO: in Inherit-Content viewCache einbauen
        /*
        root
        |-child
          |-child
        */
        $root = $this->_root;
        $child = $root->getComponentById('1');
        $childchild = $root->getComponentById('2');
        $model = Vps_Model_Abstract::getInstance('Vps_Component_Cache_Box_IcRoot_InheritContent_Child_Model');

        $render = $child->render(true, true);
        $this->assertEquals($render, '1-ic-child');
        $render = $childchild->render(true, true);
        $this->assertEquals($render, '1-ic-child');

        $row = $model->getRow('1-ic-child');
        $row->content = 'foo';
        $row->save();
        $this->_process();
        $render = $child->render(true, true);
        $this->assertEquals($render, 'foo');
        $render = $childchild->render(true, true);
        $this->assertEquals($render, 'foo');

        $model->getRow('1-ic-child')->delete();
        $this->_process();
        $render = $child->render(true, true);
        $this->assertEquals($render, 'root-ic-child');
        $render = $childchild->render(true, true);
        $this->assertEquals($render, 'root-ic-child');
    }
}
