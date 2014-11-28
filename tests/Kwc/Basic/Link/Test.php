<?php
/**
 * @group Kwc_Basic_Link
 **/
class Kwc_Basic_Link_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Link_Root');
        $this->_root->setFilename('');
    }

    public function testCacheTargetInvisible()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Link_PagesModel');
        $row = $model->getRow(2);

        $c = $this->_root->getComponentById('1');
        $this->assertRegExp('/.*<a.*href=\"\/foo\/bar">.*Testlink.*<\/a>.*/siU', $c->render());

        $row->visible = false;
        $row->save();
        $this->_process();
        $html = $c->render();
        $this->assertFalse(!!strpos($html, '<a'));
        $this->assertFalse(!!strpos($html, '</a'));
        $this->assertTrue(!!strpos($html, 'Testlink'));
    }
}
