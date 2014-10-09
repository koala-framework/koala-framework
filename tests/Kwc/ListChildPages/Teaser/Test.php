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

        $s = new Kwf_Model_Select();
        $s->whereEquals('parent_component_id', 400);
        $this->assertEquals(2, count($model->getRows($s)));
    }

    public function testModel2()
    {
        $p = $this->_root;
        $rootModel = $p->getComponent()->getChildModel();

        $listChildPages = $p->getComponentById(400);
        $model = $listChildPages->getComponent()->getChildModel();

        $s = new Kwf_Model_Select();
        $s->whereEquals('parent_component_id', 400);
        $s->whereEquals('ignore_visible', true);
        $this->assertEquals(3, count($model->getRows($s)));
    }

    public function testModel3()
    {
        $p = $this->_root;
        $rootModel = $p->getComponent()->getChildModel();

        $listChildPages = $p->getComponentById(400);
        $model = $listChildPages->getComponent()->getChildModel();

        $s = new Kwf_Model_Select();
        $s->whereEquals('parent_component_id', 400);
        $s->whereEquals('id', 501);
        $this->assertEquals(1, count($model->getRows($s)));
    }

    public function testCacheAddNewPageSetEntryVisible()
    {
        $html = $this->_root->getComponentById(401)->render();
        $pageModel = Kwf_Model_Abstract::getInstance('Kwc_ListChildPages_Teaser_PageModel');
        $pageRow = $pageModel->createRow(array('id'=>603, 'pos'=>4, 'visible'=>true,
            'name'=>'name603', 'filename' => 'name603','custom_filename' => false,
            'parent_id'=>401, 'component'=>'empty', 'is_home'=>false, 'category' =>'main',
            'hide'=>false, 'parent_subroot_id' => 'root')
        );
        $pageRow->save();
        $this->_process();
        $html2 = $this->_root->getComponentById(401)->render();

        $childModel = Kwf_Model_Abstract::getInstance('Kwc_ListChildPages_Teaser_TeaserWithChild_Child_Model');
        $childRow = $childModel->createRow(array('component_id'=>'401-603', 'visible' => true));
        $childRow->save();
        $this->_process();

        $html3 = $this->_root->getComponentById(401)->render();
        $this->assertEquals(substr_count($html, 'TEASER'), 1);
        $this->assertEquals(substr_count($html2, 'TEASER'), 1);
        $this->assertEquals(substr_count($html3, 'TEASER'), 2);
    }
}
