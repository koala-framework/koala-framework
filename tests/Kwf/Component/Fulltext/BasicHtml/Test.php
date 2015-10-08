<?php
/**
 * @group Fulltext
 */
class Kwf_Component_Fulltext_BasicHtml_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Fulltext_BasicHtml_Root');
        Kwf_Component_PagesMetaModel::getInstance()->indexRecursive($this->_root);
    }

    public function testChangeHtml()
    {
        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Fulltext_BasicHtml_Html_TestModel')->getRow('1');
        $r->content = '<p>lalelu</p>';
        $r->save();

        $this->_process();

        $row = Kwf_Component_PagesMetaModel::getInstance()->getRow(array('equals'=>array('page_id'=>'1')));
        $this->assertNotNull($row);
        $age = time() - strtotime($row->changed_date);
        $this->assertTrue($age >= 0 && $age < 5);
    }

    public function testChangeChildHtml()
    {
        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Fulltext_BasicHtml_Html_TestModel')->getRow('2-html');
        $r->content = '<p>lalelu</p>';
        $r->save();

        $this->_process();

        $row = Kwf_Component_PagesMetaModel::getInstance()->getRow(array('equals'=>array('page_id'=>'2')));
        $this->assertNotNull($row);
        $age = time() - strtotime($row->changed_date);
        $this->assertTrue($age >= 0 && $age < 5);
    }

    public function testAddPage()
    {
        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Fulltext_BasicHtml_PagesModel')->createRow();
        $r->parent_id = 'root';
        $r->component = 'html';
        $r->visible = true;
        $r->name = 'Test4';
        $r->filename = 'test4';
        $r->save();

        $this->_process();

        $row = Kwf_Component_PagesMetaModel::getInstance()->getRow(array('equals'=>array('page_id'=>'4')));
        $this->assertNotNull($row);
        $age = time() - strtotime($row->changed_date);
        $this->assertTrue($age >= 0 && $age < 5);
    }

    public function testRemovePage1()
    {
        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Fulltext_BasicHtml_PagesModel')->getRow('1');
        $r->delete();

        $this->_process();

        $row = Kwf_Component_PagesMetaModel::getInstance()->getRow(array('equals'=>array('page_id'=>'1')));
        $this->assertNotNull($row);
        $age = time() - strtotime($row->changed_date);
        $this->assertTrue($age >= 0 && $age < 5);
    }

    public function testRemovePage2()
    {
        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Fulltext_BasicHtml_PagesModel')->getRow('2');
        $r->delete();

        $this->_process();

        $row = Kwf_Component_PagesMetaModel::getInstance()->getRow(array('equals'=>array('page_id'=>'2')));
        $this->assertNotNull($row);
        $age = time() - strtotime($row->changed_date);
        $this->assertTrue($age >= 0 && $age < 5);
    }

    public function testRemovePageSetInvisible()
    {
        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Fulltext_BasicHtml_PagesModel')->getRow('1');
        $r->visible = false;
        $r->save();

        $this->_process();

        $row = Kwf_Component_PagesMetaModel::getInstance()->getRow(array('equals'=>array('page_id'=>'1')));
        $this->assertNotNull($row);
        $age = time() - strtotime($row->changed_date);
        $this->assertTrue($age >= 0 && $age < 5);
    }

    public function testChangePageType1()
    {
        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Fulltext_BasicHtml_PagesModel')->getRow('1');
        $r->component = 'empty';
        $r->save();

        $this->_process();

        $row = Kwf_Component_PagesMetaModel::getInstance()->getRow(array('equals'=>array('page_id'=>'1')));
        $this->assertNotNull($row);
        $age = time() - strtotime($row->changed_date);
        $this->assertTrue($age >= 0 && $age < 5);
        $this->assertTrue($row->changed_recursive);
    }

    public function testChangePageType2()
    {
        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Fulltext_BasicHtml_PagesModel')->getRow('2');
        $r->component = 'empty';
        $r->save();

        $this->_process();

        $row = Kwf_Component_PagesMetaModel::getInstance()->getRow(array('equals'=>array('page_id'=>'2')));
        $this->assertNotNull($row);
        $age = time() - strtotime($row->changed_date);
        $this->assertTrue($age >= 0 && $age < 5);
        $this->assertTrue($row->changed_recursive);
    }
}
