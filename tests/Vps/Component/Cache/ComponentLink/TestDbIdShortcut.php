<?php
/**
 * @group Component_Cache_ComponentLink
 * @group Component_Cache_ComponentLink_DbIdShortcut
 */
class Vps_Component_Cache_ComponentLink_TestDbIdShortcut extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_ComponentLink_DbIdShortcut_Component');
    }

    public function testLink()
    {
        $root = $this->_root;
        $this->assertEquals(1, substr_count($root->render(), '>foo<'));

        $model = $root->getComponent()->getChildModel();
        $row = $model->getRow('1');
        $row->name = 'bar';
        $row->save();
        $this->_process();

        $this->assertEquals(1, substr_count($root->render(), '>bar<'));
    }
}
