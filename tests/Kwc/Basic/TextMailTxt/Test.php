<?php
class Kwc_Basic_TextMailTxt_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_TextMailTxt_Root');
    }

    public function testMailText()
    {
//         $this->markTestIncomplete();

        $m1 = Kwf_Component_Data_Root::getInstance()->getComponentById('root_mail1');
        $text1 = $m1->getComponent()->getText();
        $this->assertEquals("xxy foo:\nhttp://vivid.com\nyyx\n", $text1); //linebreak before and after the link happens, because of the length of the created link ({cc mail: ...)
        return;
        $m2 = Kwf_Component_Data_Root::getInstance()->getComponentById('root_mail2');
        $text2 = $m2->getComponent()->getText();
    }
}
