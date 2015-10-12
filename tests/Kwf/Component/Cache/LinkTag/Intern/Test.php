<?php
/**
 * @group Component_Cache_LinkTag
 * @group Component_Cache_LinkTag_Intern
 */
class Kwf_Component_Cache_LinkTag_Intern_Test extends Kwc_TestAbstract
{
    private $_linkModel;
    private $_pagesModel;
    private $_tableModel;

    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_LinkTag_Intern_Root_Component');
        $this->_linkModel = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_LinkTag_Intern_Root_Link_Model');
        $this->_pagesModel = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_LinkTag_Intern_Root_Model');
        $this->_tableModel = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_LinkTag_Intern_Root_TableModel');
    }

    public function testFilenameChange()
    {
        $link = $this->_root->getChildComponent('_link');

        $this->assertRegExp(
            '#<a .*?href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_Intern_Root_Component/f1">#',
            $link->render(true, false)
        );

        $row = $this->_pagesModel->getRow(1);
        $row->filename = 'g1';
        $row->save();
        $this->_process();

        $this->assertRegExp(
            '#<a .*?href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_Intern_Root_Component/g1">#',
            $link->render(true, false)
        );
    }

    public function testRecursiveUrlChange()
    {
        $row = $this->_linkModel->getRow('root_link');
        $row->target = 2;
        $row->save();
        $this->_process();

        $link = $this->_root->getChildComponent('_link');

        $this->assertRegExp(
            '#<a .*?href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_Intern_Root_Component/f1/f2">#',
            $link->render(true, false)
        );

        $row = $this->_pagesModel->getRow(1);
        $row->filename = 'g1';
        $row->save();
        $this->_process();

        $this->assertRegExp(
            '#<a .*?href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_Intern_Root_Component/g1/f2">#',
            $link->render(true, false)
        );
    }

    public function testComponentLinkCache()
    {
        $this->assertRegExp(
            '#<a .*?href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_Intern_Root_Component/f1">link</a>#',
            $this->_root->render(true, false)
        );

        $row = $this->_pagesModel->getRow(1);
        $row->filename = 'g1';
        $row->save();
        $this->_process();

        $this->assertRegExp(
            '#<a .*?href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_Intern_Root_Component/g1">link</a>#',
            $this->_root->render(true, false)
        );
    }

    public function testTableFilenameChange()
    {
        $row = $this->_linkModel->getRow('root_link');
        $row->target = 'table_1_child';
        $row->save();
        $this->_process();

        $link = $this->_root->getChildComponent('_link');

        $this->assertRegExp(
            '#<a .*?href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_Intern_Root_Component/1_p1/child">#',
            $link->render(true, false)
        );

        $row = $this->_tableModel->getRow(1);
        $row->filename = 'q1';
        $row->save();
        $this->_process();

        $this->assertRegExp(
            '#<a .*?href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_Intern_Root_Component/1_q1/child">#',
            $link->render(true, false)
        );
    }

    public function testTableComponentLinkChange()
    {
        $row = $this->_linkModel->getRow('root_link');
        $row->target = 'table_1_child';
        $row->save();
        $this->_process();

        $this->assertRegExp(
            '#<a .*?href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_Intern_Root_Component/1_p1/child">link</a>#',
            $this->_root->render(true, false)
        );

        $row = $this->_tableModel->getRow(1);
        $row->filename = 'q1';
        $row->save();
        $this->_process();

        $this->assertRegExp(
            '#<a .*?href="/kwf/kwctest/Kwf_Component_Cache_LinkTag_Intern_Root_Component/1_q1/child">link</a>#',
            $this->_root->render(true, false)
        );
    }

}
