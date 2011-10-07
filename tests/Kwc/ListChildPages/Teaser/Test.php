<?php
/**
 * @group ListChildPages
 * @group ListChildPages_Teaser
 */
class Vpc_ListChildPages_Teaser_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_ListChildPages_Teaser_Root');
    }

    public function testModel()
    {
        $p = $this->_root;
        $rootModel = $p->getComponent()->getChildModel();

        $listChildPages = $p->getComponentById(400);
        $model = $listChildPages->getComponent()->getChildModel();

        $s = new Vps_Model_Select();
        $s->whereEquals('parent_component_id', 400);
        $this->assertEquals(2, count($model->getRows($s)));

        $s = new Vps_Model_Select();
        $s->whereEquals('parent_component_id', 400);
        $s->whereEquals('ignore_visible', true);
        $this->assertEquals(3, count($model->getRows($s)));

        $s = new Vps_Model_Select();
        $s->whereEquals('parent_component_id', 400);
        $s->whereEquals('id', 501);
        $this->assertEquals(1, count($model->getRows($s)));
    }
}
