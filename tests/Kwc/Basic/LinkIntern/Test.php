<?php
/**
 * @group Kwc_Basic_LinkIntern
 **/
class Kwc_Basic_LinkIntern_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_LinkIntern_Root');
        $this->_root->setFilename('');
    }

    public function testCacheTargetInvisible()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_LinkIntern_PagesModel');
        $row = $model->getRow(2);

        $c = $this->_root->getComponentById('1');
        $this->assertRegExp('<a .*href="/foo/bar".*>', $c->render());

        $row->visible = false;
        $row->save();
        $this->_process();
        $this->assertEquals('', $c->render());

        $row->visible = true;
        $row->save();
        $this->_process();
        $this->assertRegExp('<a .*href="/foo/bar".*>', $c->render());
    }

    public function testCacheTargetDelete()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_LinkIntern_PagesModel');
        $row = $model->getRow(2);

        $c = $this->_root->getComponentById('1');
        $this->assertRegExp('<a .*href="/foo/bar".*>', $c->render());

        $row->delete();
        $this->_process();
        $this->assertEquals('', $c->render());
    }
}
