<?php
/**
 * @group Component_Cache
 * @group Component_Cache_Box
 */
class Vps_Component_Cache_Box_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_Box_Root_Component');
    }

    public function testBox()
    {
        $root = $this->_root;
        $child = $root->getChildComponent('_child');

        $render = $root->render(true, true);
        $this->assertEquals($render, 'root-box root-boxUnique');
        $render = $child->render(true, true);
        $this->assertEquals($render, 'root_child-box root-boxUnique');

        $model = Vps_Model_Abstract::getInstance('Vps_Component_Cache_Box_Root_Box_Model');
        $row = $model->getRow('root-boxUnique');
        $row->content = 'foo';
        $row->save();
        $this->_process();
        $render = $root->render(true, true);
        $this->assertEquals($render, 'root-box foo');
        $render = $child->render(true, true);
        $this->assertEquals($render, 'root_child-box foo');

        $model = Vps_Model_Abstract::getInstance('Vps_Component_Cache_Box_Root_Box_Model');
        $row = $model->getRow('root-box');
        $row->content = 'bar';
        $row->save();
        $row = $model->getRow('root_child-box');
        $row->content = 'foobar';
        $row->save();
        $this->_process();
        $render = $root->render(true, true);
        $this->assertEquals($render, 'bar foo');
        $render = $child->render(true, true);
        $this->assertEquals($render, 'foobar foo');
    }
}
