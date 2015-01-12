<?php
/**
 * @group ListChildPages
 * @group ListChildPages_Teaser
 */
class Kwc_ListChildPages_Teaser_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_ListChildPages_Teaser_Root');
    }

    public function testModel1()
    {
        $p = $this->_root;
        $rootModel = $p->getComponent()->getChildModel();

        $listChildPages = $p->getComponentById(400);
        $model = $listChildPages->getComponent()->getChildModel();
        $model->updatePages($listChildPages);

        $s = new Kwf_Model_Select();
        $s->whereEquals('component_id', 400);
        $this->assertEquals(3, count($model->getRows($s)));
    }
}
