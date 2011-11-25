<?php
/**
 * @group Component_Cache
 * @group Component_Cache_Visible
 */
class Kwf_Component_Cache_Visible_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_Visible_Root_Component');
    }

    public function testDirectory()
    {
        $contentModel = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Visible_Root_Child_Model');
        $directoryModel = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Visible_Root_DirectoryModel');

        $this->assertEquals('foo', $this->_render('root-1_child'));

        $row = $directoryModel->getRow('1');
        $row->visible = false;
        $row->save();
        $this->_process();

        // Test fails at the end with following line:
        // $this->assertEquals('foo', $this->_render('root-1_child'));
        // components which are not visible should not be cached. however, due to
        // performance issues this is not checked (except in Kwf_Data_Kwc_Frontend)

        $row = $contentModel->getRow('root-1_child');
        $row->content = 'bar';
        $row->save();

        $row = $directoryModel->getRow('1');
        $row->visible = true;
        $row->save();
        $this->_process();

        $this->assertEquals('bar', $this->_render('root-1_child'));
    }

    public function testPages()
    {
        $contentModel = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Visible_Root_Child_Model');
        $pagesModel = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Visible_Root_PagesModel');

        $this->assertEquals('foo', $this->_render('2_child'));

        $row = $pagesModel->getRow('1');
        $row->visible = false;
        $row->save();
        $this->_process();

        // Test fails at the end with following line:
        // $this->assertEquals('foo', $this->_render('2_child'));
        // components which are not visible should not be cached. however, due to
        // performance issues this is not checked (except in Kwf_Data_Kwc_Frontend)

        $row = $contentModel->getRow('2_child');
        $row->content = 'bar';
        $row->save();

        $row = $pagesModel->getRow('1');
        $row->visible = true;
        $row->save();
        $this->_process();

        $this->assertEquals('bar', $this->_render('2_child'));
    }

    private function _render($componentId)
    {
        return $this->_root
            ->getComponentById($componentId, array('ignoreVisible' => true))
            ->render(true, false);
    }
}
