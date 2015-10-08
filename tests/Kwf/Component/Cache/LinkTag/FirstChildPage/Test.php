<?php
/**
 * @group Component_Cache_LinkTag
 * @group Component_Cache_LinkTag_FirstChildPage
 */
class Kwf_Component_Cache_LinkTag_FirstChildPage_Test extends Kwc_TestAbstract
{
    private $_pagesModel;
    private $_tableModel;

    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component');
        /*
        -root
         -1
          -2 (linktag_firstchildpage)
           -3
           -4
         -5
         */
        $this->_pagesModel = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Model');
        $this->_tableModel = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_LinkTag_FirstChildPage_Root_TableModel');
    }

    public function testParentFilenameChange()
    {
        $link = $this->_root->getComponentById(2);

        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/f1/f2/f3">f2</a>',
            $this->_root->render(true, false)
        );

        $row = $this->_pagesModel->getRow(1);
        $row->filename = 'g1';
        $row->save();
        $this->_process();

        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/g1/f2/f3">f2</a>',
            $this->_root->render(true, false)
        );
    }

    public function testChildFilenameChange()
    {
        $link = $this->_root->getComponentById(2);

        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/f1/f2/f3">f2</a>',
            $this->_root->render(true, false)
        );

        $row = $this->_pagesModel->getRow(3);
        $row->filename = 'g3';
        $row->save();
        $this->_process();

        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/f1/f2/g3">f2</a>',
            $this->_root->render(true, false)
        );
    }

    public function testChildPositionChange()
    {
        $link = $this->_root->getComponentById(2);

        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/f1/f2/f3">f2</a>',
            $this->_root->render(true, false)
        );

        $row = $this->_pagesModel->getRow(4);
        $row->pos = 1;
        $row->save();
        $row = $this->_pagesModel->getRow(3);
        $row->pos = 2;
        $row->save();
        $this->_process();

        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/f1/f2/f4">f2</a>',
            $this->_root->render(true, false)
        );
    }

    public function testPageDeleteAdd()
    {
        $link = $this->_root->getComponentById(2);

        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/f1/f2/f3">f2</a>',
            $this->_root->render(true, false)
        );

        // delete 3, childpage must be 4
        $row = $this->_pagesModel->getRow(3);
        $row->delete();
        $this->_process();
        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/f1/f2/f4">f2</a>',
            $this->_root->render(true, false)
        );

        // make 4 invisible, childpage must be empty
        $row = $this->_pagesModel->getRow(4);
        $row->visible = false;
        $row->save();
        $this->_process();
        $this->assertEquals(
            '',
            $this->_root->render(true, false)
        );

        // make 4 visible, childpage must be 4
        $row = $this->_pagesModel->getRow(4);
        $row->visible = true;
        $row->save();
        $this->_process();
        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/f1/f2/f4">f2</a>',
            $this->_root->render(true, false)
        );

        // delete 4, childpage must be empty
        $row = $this->_pagesModel->getRow(4);
        $row->delete();
        $this->_process();
        $this->assertEquals(
            '',
            $this->_root->render(true, false)
        );

        // create 6 under 2, childpage must be 6
        $row = $this->_pagesModel->createRow(
            array('id'=>6, 'pos'=>1, 'visible'=>true, 'name'=>'f6', 'filename' => 'f6',
                  'parent_id'=>2, 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null)
        );
        $row->save();
        $this->_process();
        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/f1/f2/f6">f2</a>',
            $this->_root->render(true, false)
        );

        // move 6 under root, childpage must be empty
        $row = $this->_pagesModel->getRow(6);
        $row->parent_id = 'root';
        $row->save();
        $this->_process();
        $this->assertEquals(
            '',
            $this->_root->render(true, false)
        );

        // move 6 under 2, childpage must be 6
        $row = $this->_pagesModel->getRow(6);
        $row->parent_id = 2;
        $row->save();
        $this->_process();
        $this->assertEquals(
            '<a href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component/f1/f2/f6">f2</a>',
            $this->_root->render(true, false)
        );
    }
}
