<?php
/**
 * @group Component_Cache
 * @group Component_Cache_Composite
 */
class Kwf_Component_Cache_Composite_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_Composite_Root_Component');
    }

    public function testComposite()
    {
        $root = $this->_root;
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Composite_Root_C1_Model');

        $this->assertEquals('foo', $root->render());

        $row = $model->getRow('root-c1');
        $row->has_content = false;
        $row->save();
        $this->_process();
        $this->assertEquals('', $root->render(false));
        $this->assertEquals('', $root->render());
    }
}
