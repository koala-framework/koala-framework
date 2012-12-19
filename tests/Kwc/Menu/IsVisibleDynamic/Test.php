<?php
class Kwc_Menu_IsVisibleDynamic_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Menu_IsVisibleDynamic_Root_Component');

        Kwc_Menu_IsVisibleDynamic_Test_Component::$invisibleIds = array();
    }

    public function testIt()
    {
        $m = $this->_root->getComponentById('root');
        $html = $m->render(true, true);
        $this->assertContains('f1', $html);
        $this->assertContains('f2', $html);
        $this->assertContains('f3', $html);
        $this->assertEquals(3, substr_count($html, '<li'));

        //now hide f2 and render again. no clear cache required as it should hide dynamically.
        Kwc_Menu_IsVisibleDynamic_Test_Component::$invisibleIds = array(
            '2'
        );
        $html = $m->render(true, true);
        $this->assertEquals(2, substr_count($html, '<li'));
        $this->assertContains('f1', $html);
        $this->assertNotContains('f2', $html);
        $this->assertContains('f3', $html);
    }
}
